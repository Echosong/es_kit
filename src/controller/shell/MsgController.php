<?php

/**
 * Created by PhpStorm.
 * User: LJC
 * Date: 2017/10/9
 * Time: 10:46
 */
class MsgController extends BaseController
{

    /**
     * 记得上线以后调整频率
     * 微信群发功能 nohup php index.php shell msg financequery > /usr/logs/yh-financeQuery-send.out &
     */
    public function actionFinanceQuery () {
        cli_set_process_title('yh-financequery-send');
        $redis = App::redis();
        while (true) {
            $out_trade_no = $redis->rPop(Sms::PUSH_FINANCE_QUERY);
            if ($redis->exists($out_trade_no)) {
                $financePayDb = new FinancePay();
                $state = 0;
                //查询
                $financePay = json_decode($redis->get($out_trade_no), true);
                $payConfigDb = new PayConfig();
                $payConfig = $payConfigDb->find(['company_id' => $financePay['company_id'], 'type' => $financePay['type']]);
                if ($financePay['type'] == FinancePay::WEIXIN) {
                    $wxPay = App::wxPay();
                    $wxPay->setSubMerchant($payConfig['mch_id'], $payConfig['appid']);
                    try {
                        $result = $wxPay->order->queryByOutTradeNumber($out_trade_no);
                        if ($result['return_code'] == 'SUCCESS') {
                            if ($result['result_code'] == 'SUCCESS') {
                                if ($result['trade_state'] == 'SUCCESS') {
                                    $state = FinancePay::SUCCESS;
                                    $returnVal = '交易支付成功';
                                } else if ($result['trade_state'] != 'USERPAYING') {
                                    $state = FinancePay::ERROR;
                                    $returnVal = $result['trade_state'];
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $redis->lPush(Sms::PUSH_FINANCE_QUERY, $out_trade_no);
                    }
                } elseif ($financePay['type'] == FinancePay::ALIPAY) {
                    $alipay = App::aliPay();
                    $request = new Alipay\Request\AlipayTradeQueryRequest();
                    $request->setBizContent(['out_trade_no' => $out_trade_no]);
                    $request->setAppAuthToken($payConfig['appkey']);
                    try {
                        //交易状态：WAIT_BUYER_PAY（交易创建，等待买家付款）、TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、TRADE_SUCCESS（交易支付成功）、TRADE_FINISHED（交易结束，不可退款）
                        $result = $alipay->execute($request)->getData();
                        if ($result['code'] == 10000) {
                            if ($result['trade_status'] === 'TRADE_CLOSED') {
                                $state = FinancePay::ERROR;
                                $returnVal = '未付款交易超时关闭，或支付完成后全额退款';
                            } else if ($result['trade_status'] === 'TRADE_SUCCESS') {
                                $state = FinancePay::SUCCESS;
                                $returnVal = '交易支付成功';
                            }
                        }
                    } catch (Exception $e) {
                        $redis->lPush(Sms::PUSH_FINANCE_QUERY, $out_trade_no);
                    }
                }
                if ($state) {
                    $financePayDb->update(['id' => $financePay['id']], ['state' => $state, 'return_info' => $returnVal]);
                } else {
                    $redis->lPush(Sms::PUSH_FINANCE_QUERY, $out_trade_no);
                }
            }
            sleep(1);
        }
    }

    /**
     * 微信群发功能 nohup php index.php shell msg sendallwx > /usr/logs/yh-group-send &
     */
    public function actionSendAllWX () {
        cli_set_process_title('yh-group-send');
        $redis = App::redis();
        while (true) {

            $chatLstr = $redis->brPop(Sms::GROUP_REDIS_LIST, 10);
            if (!$chatLstr) {
                continue;
            }
            $smsinfo = $chatLstr[1];
            $sms = json_decode($smsinfo, true);
            $param = json_decode($sms['param'], true);
            $userDb = new User();
            if ($param['type'] == 'user_staff') {
                $oauths = $userDb->query("select a.id as id , a.openid as openid from tb_user as a ,tb_staff as b 
where b.company_id = {$param['company_id']} and b.level = 0 and b.state = 1 and b.get_msg = 1 and a.staff_id = b.id");
            } elseif ($param['type'] == 'user') {
                $oauths = $userDb->findAll(["openid is not null and company_id = {$param['company_id']}"], 'id asc',
                                           'openid,id');
            } elseif ($param['type'] == 'staff') {//共享版只向管理员发送群推
                $staffDb = new Staff();
                $oauths = $staffDb->findAll(["openid is not null and level = 0  and company_id = {$param['company_id']}"],
                                            'id asc', 'openid,id');
            }
            $smsDb = new Sms();
            foreach ($oauths as $v) {
                $msg = str_replace('${openid}', $v['openid'], $sms['param']);
                $param = json_decode($msg, true);
                $app = $this->getOfficial($param['company_id']);
                $result = Common::sendTemplateMsg($app, $param);
                if ($result['errcode'] == 0) {
                    echo "推送给用户{$param['type']}{$v['id']}: {$v['openid']}成功" . PHP_EOL;
                } else {
                    echo "推送给用户{$param['type']}{$v['id']}: {$v['openid']}失败{$result['errmsg']}" . PHP_EOL;
                }
            }
            $smsDb->update(['id' => $sms['id']], ['state' => 1]);
            echo '任务处理完毕 sms 编号：' . $sms['id'];
        }
    }


    /**
     * 发送短信脚本 nohup php index.php shell msg sendsms > /usr/logs/yh-sms-process.out &
     */
    public function actionSendSms () {
        cli_set_process_title('yh-sms-process');
        $redis = App::redis();
        while (true) {

            $chatLstr = $redis->brPop(Sms::PUSH_REDIS_LIST, 10);
            if (!$chatLstr) {
                continue;
            }
            $smsinfo = $chatLstr[1];
            $sms = json_decode($smsinfo, true);
            switch ($sms['type']) {
                case 1:
                    break;
                case 4:

                    break;
                case 2: //app推送
                    break;  //
                case 3: //邮件类型
                    break;
                case Sms::SEND_WX_TEMPLATE:
                    $param = json_decode($sms['param'], true);
                    $app = $this->getOfficial($param['company_id']);
                    $result = Common::sendTemplateMsg($app, $param);
                    $resultJson = json_encode($result);
                    $returnVal = ['code' => ($result['errcode'] == 0), 'msg' => '模版推送', 'content' => $resultJson];
                    break;
            }
            $smsDb = new Sms();
            if ($returnVal['code']) {
                $smsDb->update(['id' => $sms['id']], ['state' => 1]);
                echo '向用户' . $sms['mobile'] . '发送:' . $returnVal['msg']
                    . $sms['param'] . ' =>发送成功!' . PHP_EOL;
            } else {
                Helper::log(
                    $smsinfo . ',发送' . $returnVal['msg'] . '失败,返回结果：'
                    . $returnVal['content'], "Sms"
                );
                $smsDb->update(['id' => $sms['id']], ['state' => 2]);
                echo '向用户' . $sms['mobile'] . ' =>' . $returnVal['msg']
                    . '发送失败 ：' . $returnVal['content'] . PHP_EOL;
            }
        }
    }

    /**
     * 获取微信公众号实列子
     */
    protected function getOfficial ($companyId) {
        $openPlatform = App::wxThird();
        $companyDb = new Company();
        $company = $companyDb->find(['id' => $companyId]);
        $companyAuthDb = new CompanyAuth();
        //共享版本
        if ($company['type'] == 3) {
            $companyAuth = $companyAuthDb->find(['type' => 0]);
        } else {
            $companyAuth = $companyAuthDb->find(['company_id' => $companyId]);
        }
        $officialAccount = $openPlatform->officialAccount($companyAuth['appid'], $companyAuth['refresh_token']);
        Helper::log($companyAuth['appid'], 'app_id');
        return $officialAccount;
    }

    /**
     * 发送短信脚本 nohup php index.php shell msg sendMail> /usr/logs/hlj-email-process.out &
     */
    public function actionSendMail () {
        cli_set_process_title('hlj-email-process');
        $redis = App::redis();
        while (true) {

            $chatLstr = $redis->brPop(AttachmentEmail::ATTACHMENT_REDIS_LIST, 10);
            if (!$chatLstr) {
                continue;
            }
            $emailinfo = $chatLstr[1];
            $email = json_decode($emailinfo, true);
            $body = sprintf(
                '<a href="%s">%s【点击下载】</a>', $email['attachment'],
                $email['name']
            );
            Helper::log($body, 'mail-body');
            $mailer = App::mailer();
            try {
                $message = (new Swift_Message('红辣椒约驾'))
                    ->setFrom(['Domain@dianbaobao.com' => 'Domain'])
                    ->setTo([$email['email']])
                    ->setContentType("text/html")
                    ->setBody($body);
                $ok = $mailer->send($message);
            } catch (Exception $ex) {
                Helper::log(json_encode($ex), 'mail-ex');
            }
            $attachmentEmailDb = new Model('attachment_email');
            if ($ok) {
                $attachmentEmailDb->update(
                    ['id' => $email['id']], ['status' => 1]
                );
                echo ' 邮件发送成功!' . PHP_EOL;
            } else {
                Helper::log($emailinfo . '发送邮件失败,返回结果：' . strval($ok), "mail");
                $attachmentEmailDb->update(
                    ['id' => $email['id']], ['status' => 2]
                );
                echo ' 邮件发送失败!' . PHP_EOL;
            }
        }
    }



    /**
     * 预售和结束提醒
     */
    public function actionWxAlert () {
        cli_set_process_title('hlj-wx-alert');
        while (true) {

        }
    }

    /**
     * 订单付款成功第二天给分销商算账户  nohup php index.php shell msg rebate> /usr/logs/hlj-back-rebate.out &
     */
    public function actionRebate () {
        cli_set_process_title('hlj-back-rebate');
        $financeRebateDb = new FinanceRebate();
        while (true) {

            $financeRebates = $financeRebateDb->findAll('status=0 '); //and created < DATE_SUB(now(),INTERVAL -1 hour)
            $financeLogDb = new FinanceLog();
            if ($financeRebates) {
                foreach ($financeRebates as $k => $v) {
                    $event = 0;
                    $info = '';
                    switch ($v['level']) {
                        case 1://一级返利
                            $event = FinanceLog::EVENT_DISTRIBUTOR_IN;
                            $info = '一级经销商获取返利';
                            break;
                        case 2:
                            $event = FinanceLog::EVENT_DISTRIBUTOR_IN_TOW;
                            $info = '二级经销商获取返利';
                            break;
                        case 3:
                            $event = FinanceLog::EVENT_PARTNER_IN;
                            $info = '区县合伙人获取返利';
                            break;
                    }
                    $financeLogDb->calculateMoney($v['userid'], $event, $v['money'], FinanceLog::MONEY_TYPE_ADD,
                                                  $info);
                    echo date('Y-m-m H:i:s') . $info . $v['id'] . '成功' . PHP_EOL;
                    $financeRebateDb->update(['id' => $v['id']], ['status' => 1]);
                }
            }
            echo '我在工作中...' . PHP_EOL;

            sleep(10);
        }
    }



    //经营报告发送  nohup php index.php shell msg businessreport > /usr/logs/yh-businessreport-send.out &
    public function actionBusinessReport () {
        cli_set_process_title('yh-businessreport-send');
        $companyDb = new Company();
        $smsDb = new Sms();
        $sendData = [];
        while (true) {
            $checkDayStr = date('Y-m-d ', time());
            $timeStart = strtotime($checkDayStr . ' 10:00:01');
            $timeEnd = strtotime($checkDayStr . ' 10:10:01');
            if (time() >= $timeStart && time() < $timeEnd) {
                $sendData = $this->sendReport($companyDb);
            }
            if (empty($sendData)) {
                echo   date('Y-m-d H:i:s'). '等待获取到数据...'.PHP_EOL;
                sleep(10);
                continue;
            }
            echo  date('Y-m-d H:i:s'). '获取到数据'.PHP_EOL;
            //配置表发送时间
            $sql = "select company_id ,`value` from tb_config where company_id > 0 and `key` = 'report_send_time'";
            $configs = $companyDb->query($sql);
            $data = [];
            foreach ($sendData[1] as $company) {
                //发送时间
                foreach ($configs as $ck => $config) {
                    if ($config['company_id'] == $company['id']) {
                        $data[$company['id']] = $config['value'];
                    }
                }
            }
            foreach ($data as $cid => $time) {
                $timeStart = strtotime($checkDayStr . ' ' . $time);
                $timeEnd = strtotime($checkDayStr . ' ' . $time) + 600;
                if (time() >= $timeStart && time() < $timeEnd) {
                    $smsDb->business_report($sendData[0][$cid]);
                    echo  date('Y-m-d H:i:s'). '入库成功'.PHP_EOL;
                }
            }
            echo   date('Y-m-d H:i:s'). '工作中'.PHP_EOL;
                sleep(600);
        }
    }

    protected function sendReport ($companyDb) {
        $companys = $companyDb->findAll(
            "state = 1 and app_id is not null and starttime <= NOW() and expiration >= NOW()",
            'id asc', 'id,type,name');
        $state = FinancePay::SUCCESS;

        $diffTime = ' and  created > SUBDATE(CURDATE(),INTERVAL 2 DAY) and created<CURDATE() group by company_id';

        //总数
        $sql = "select  company_id , count(*) as pay_num , sum(money) as money,sum(real_money)as real_money 
from tb_finance_pay  where userid is not null and state = {$state} {$diffTime}   ";
        $financePayTotal = $companyDb->query($sql);
        //充值
        $sql = "select  company_id , count(*) as pay_num , sum(money) as money,sum(real_money)as real_money 
from tb_finance_pay  where userid is not null and state = {$state} and business_type = 3  
{$diffTime}  ";
        $financePayRecharge = $companyDb->query($sql);

        //会员新增
        $sql = "select company_id , count(*) as num from tb_user where  iscard = 1 {$diffTime}  ";
        $member_new = $companyDb->query($sql);

        //会员总人数
        $sql = "select company_id , count(*) as num from tb_user where iscard = 1 group by company_id";
        $members = $companyDb->query($sql);

        //粉丝新增
        $sql = "select company_id , count(*) as num from tb_user where 1=1 {$diffTime}  ";
        $funs_adds = $companyDb->query($sql);

        //取关人数
        $sql = "select company_id , count(*) as num from tb_user where `state` = 'UNSUBSCRIBE' group by company_id";
        $funs_dels = $companyDb->query($sql);

        //粉丝总人数
        $sql = "select company_id , count(*) as num from tb_user where `state` <> 'UNSUBSCRIBE' group by company_id";
        $funss = $companyDb->query($sql);

        $data = [];
        foreach ($companys as $k => $company) {
            $array = [
                'company_id' => $company['id'],
                'brand_name' => $company['name'],
                'url' => '#',
                'pay_num'=>0,
                'pay_money'=>0,
                'member_new'=>0,
                'member'=>0,
                'income'=>0,
                'recharge_num'=>0,
                'recharge'=>0,
                'funs_add'=>0,
                'funs_del'=>0,
                'funs'=>0
            ];
            if ($company['type'] == Company::THREE) {
                $array['type'] = 'staff';
            } else {
                $array['type'] = 'user_staff';
            }

            //支付
            foreach ($financePayTotal as $total) {
                if ($total['company_id'] == $company['id']) {
                    $array['pay_num'] = $total['pay_num'];
                    $array['pay_money'] = $total['real_money'];
                }
            }

            //充值
            foreach ($financePayRecharge as $recharge) {
                if ($recharge['company_id'] == $company['id']) {
                    $array['recharge_num'] = $recharge['pay_num'];
                    $array['recharge'] = $recharge['money'];
                    $array['income'] = $recharge['real_money'];
                }
            }

            //会员新增
            foreach ($member_new as $new) {
                if ($new['company_id'] == $company['id']) {
                    $array['member_new'] = $new['num'];
                }
            }
            //会员人数
            foreach ($members as $member) {
                if ($member['company_id'] == $company['id']) {
                    $array['member'] = $member['num'];
                }
            }
            //粉丝新增
            foreach ($funs_adds as $funs_add) {
                if ($funs_add['company_id'] == $company['id']) {
                    $array['funs_add'] = $funs_add['num'];
                }
            }

            //粉丝取关
            foreach ($funs_dels as $funs_del) {
                if ($funs_del['company_id'] == $company['id']) {
                    $array['funs_del'] = $funs_del['num'];
                }
            }
            //粉丝总人数
            foreach ($funss as $funs) {
                if ($funs['company_id'] == $company['id']) {
                    $array['funs'] = $funs['num'];
                }
            }

            $data[$company['id']] = $array;

            //  $smsDb->business_report($array);
        }
        return [$data, $companys];

    }


}