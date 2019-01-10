<?php

/**
 * 公用的非业务性方法
 * User: LJC
 * Date: 2017/9/29
 * Time: 14:53
 */

use GuzzleHttp\Client;

class Common
{
    /*
   * OSS文件上传
   * */
    public static function upload ($limit = 0.5, $type = 0) {
        $files = $_FILES;
        $paths = [];
        $names = [];
        $arr = [];
        foreach ($files as $v) {
            $arr[] = $v['size'];
            $path = date('Y/m/d/') . uniqid() . ".jpg";
            App::oss()->uploadFile(
                $GLOBALS['oss']['Bucket'], $path, $v['tmp_name']
            );
            if ($type != 1) {
                $path .= "?" . (App::oss())::OSS_PROCESS
                    . "=image/resize,w_300";
                if ($v['size'] > $limit * 1024 * 1024) {
                    $path .= '/quality,Q_50';
                }
            }
            array_push($paths, $GLOBALS['oss']['ossUrl'] . $path);
            array_push($names, $v['name']);
        }
        return [
            'msg' => '上传成功', 'paths' => $paths, 'names' => $names,
            'size' => $arr
        ];
    }

    /*
 * OSS文件上传 不控制大小 特殊图片上传
 * */
    public static function upload1 ($limit = 2, $height = null, $width = null) {
        $files = $_FILES;
        $paths = [];
        $names = [];
        $arr = [];
        foreach ($files as $v) {
            if ($v['size'] > $limit * 1024 * 1024) {
                return [
                    'msg' => '上传失败,不能大于' . $limit . 'M', 'paths' => $paths,
                    'names' => $names, 'size' => $arr
                ];
            }
            $arr[] = $v['size'];
            $hou = substr(strrchr($v['name'], '.'), 1);
            if ($hou == 'jpeg') {
                $hou = 'jpg';
            }
            $path = date('Y/m/d/') . uniqid() . "." . $hou;
            App::oss()->uploadFile(
                $GLOBALS['oss']['Bucket'], $path, $v['tmp_name']
            );
            array_push($paths, $GLOBALS['oss']['ossUrl'] . $path);
            array_push($names, $v['name']);
        }
        return [
            'msg' => '上传成功', 'paths' => $paths, 'names' => $names,
            'size' => $arr
        ];
    }

    //浏览器下载图片
    public function downImg ($url) {
        $mime = 'application/force-download';
        header('Pragma: public'); // required
        header('Expires: 0'); // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($url) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($url); // push it out
        exit;
    }

    /**
     * wx下载文件到本地
     * @param int $userid 站长UID
     * @param int $vipid 分站ID
     * @param bool $type 默认为false,修改和添加分站时. 删除分站时为true,还原各用户vip_id字段为总站
     * @return bool
     */
    public static function wxDownloadFile ($url) {
        //方法一：//推荐用该方法
        $header = array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',);
        // $url='http://mmbiz.qpic.cn/mmbiz_png/6zuH0H9ttkOuC1TR7x3sywEVBP9B7u5FcVktdNotiajVe2LVbq85ibEFl8NeIxLGPqcDo104lJqr07A1jlTvYZxw/0?wx_fmt=png';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {//把URL格式的图片转成base64_encode格式的！
            $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
        }
        $img_content = $imgBase64Code;//图片内容
        //echo $img_content;exit;

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
            $type = $result[2];//得到图片类型png?jpg?gif?
            $tmpFile = sys_get_temp_dir() . DS . uniqid() . '.' . $type;
            // $new_file = "./cs/cs.{$type}";

            if (file_put_contents($tmpFile, base64_decode(str_replace($result[1], '', $img_content)))) {
                $path = date('Y/m/d/') . uniqid() . "." . $type;
                App::oss()->uploadFile(
                    $GLOBALS['oss']['Bucket'], $path, $tmpFile
                );
                unlink($tmpFile);
                return $GLOBALS['oss']['ossUrl'] . $path;
            }
        }
        return false;

    }

    /**
     * oss下载文件到本地
     * @param int $userid 站长UID
     * @param int $vipid 分站ID
     * @param bool $type 默认为false,修改和添加分站时. 删除分站时为true,还原各用户vip_id字段为总站
     * @return bool
     */
    public static function downloadFile ($bigImage) {
        preg_match_all("/(.*?(\.jpg|\.png))/", $bigImage, $arr);
        $filePath = substr($arr[0][0], -28);
        $tmpFile = sys_get_temp_dir() . DS . uniqid() . '.jpg';
        $options = array(
            (App::oss())::OSS_FILE_DOWNLOAD => $tmpFile
        );
        (App::oss())->getObject(
            $GLOBALS['oss']['Bucket'], $filePath, $options
        );
        return $tmpFile;
    }

    /*
     * OSS文件删除
     * */
    public static function delete ($file) {
        $path = str_replace($GLOBALS['oss']['ossUrl'], '', $file);
        try {
            App::oss()->deleteObject($GLOBALS['oss']['Bucket'], $path);
            return true;
        } catch (OssException $e) {
            return false;
        }
    }

    /*
    * 字符串截取首尾长度 如  （123456，1）=》 1****6
    * */
    function strDeal ($str, $len) {
        if (mb_strlen($str) >= $len) {
            $chars = '';
            if (mb_strlen($str) > 2 * $len + 1) {
                for ($i = 0; $i < mb_strlen($str) - 2 * $len; $i++) {
                    $chars .= '*';
                }
            } else {
                $chars .= '***';
            }
            return mb_substr($str, 0, $len, 'UTF-8') . $chars . mb_substr(
                    $str, mb_strlen($str) - $len, $len, 'UTF-8'
                );
        } else {
            return $str . '***' . $str;
        }
    }

    const KEY = "fb57583391ee92b2b93b9be67bbf5386";

    public static function gaodeLocation ($address, $city = "全国") {

        $client = new Client(
            [
                'base_uri' => 'http://restapi.amap.com/v3/geocode/geo',
                'timeout' => 2.0,
            ]
        );
        $response = $client->request(
            'GET', '', [
                     'query' => [
                         'key' => self::KEY, 'address' => $address, 'city' => $city
                     ]
                 ]
        );
        return $response->getBody()->getContents();
    }

    /**
     * 根据经纬度获取城市
     *
     * @param $location
     */
    public static function gaodeAddress ($location) {
        $client = new Client(
            [
                'base_uri' => 'http://restapi.amap.com/v3/geocode/regeo',
                'timeout' => 2.0,
            ]
        );
        $response = $client->request(
            'GET', '', [
                     'query' => ['key' => self::KEY, 'location' => $location]
                 ]
        );
        return json_decode(
            $response->getBody()->getContents(), true
        )['regeocode'];
    }

    /**
     *  获取头部信息
     *
     * @return mixed
     *
     */
    public static function getallheaders () {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(
                    ' ', '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                )]
                    = $value;
            }
        }
        return $headers;
    }

    /**
     * @param        $msg
     * @param string $url
     * @param int $code 非0 错误提示
     */
    public static function redirectTop ($msg, $url = '', $code = 0) {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])
            && strtolower(
                $_SERVER["HTTP_X_REQUESTED_WITH"]
            ) == "xmlhttprequest"
        ) {
            if (is_array($msg)) {
                exit(json_encode($msg));
            } else {
                Helper::responseJson(
                    ['alertStr' => $msg, 'redirect' => $url], $code
                );
            }
        } else {
            $strAlert = "";
            if (!empty($msg)) {
                $strAlert = "alert(\"{$msg}\");";
            }
            if ($url == "") {
                exit("<script>alert('$msg');window.history.go(-1);</script>");
            } else {
            }
            exit("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){  {$strAlert} top.location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>");
        }
    }

    /**
     * 发送微信模板消息
     *
     * @param $msg
     */
    public static function sendTemplateMsg ($app, $msg) {
        unset($msg['company_id']);
        unset($msg['type']);
        $result = $app->template_message->send($msg);
        // Helper::log(json_encode($result), 'wx_template_send');
        return $result;
    }

    /**
     * 发送post请求
     *
     * @param string $url
     * @param string $param
     *
     * @return bool|mixed
     */
    public static function request_post ($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }


    /**
     *  生成CSV文件并返回OSS路径
     *
     * @param array $data 查询结果 二维数组
     * @param string $filename 文件名
     */
    public static function getPubOssCsv ($data, $filename = 'book.csv') {
        if (empty($data)) {
            return false;
        }
        ob_start();
        $tmpFile = sys_get_temp_dir() . DS . $filename;
        $file = fopen($tmpFile, 'w');
        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file);
        foreach ($data as $k => $v) {
            fputcsv($file, $v);
        }
        App::oss()->uploadFile(
            $GLOBALS['oss']['Bucket'], $filename, $tmpFile
        );
        fclose($file);
        unlink($tmpFile);
        return $GLOBALS['oss']['ossUrl'] . $filename;
    }


    /**
     *导出CSV
     *
     * @param $data      array  要下载的数据
     * @param $title_arr array　表头
     * @param $filename  string　文件名称
     */
    public static function export_csv ($data, $title_arr, $file_name = '') {
        ini_set("max_execution_time", "3600");

        $csv_data = '';

        /** 标题 */
        $nums = count($title_arr);

        for ($i = 0; $i < $nums - 1; ++$i) {
            //$csv_data .= '"' . $title_arr[$i] . '",';
            $csv_data .= $title_arr[$i] . ',';
        }
        if ($nums > 0) {
            $csv_data .= $title_arr[$nums - 1] . "\r\n";
        }

        foreach ($data as $k => $row) {
            $_tmp_csv_data = '';
            foreach ($row as $key => $r) {
                $row[$key] = str_replace("\"", "\"\"", $r);

                if ($_tmp_csv_data == '') {
                    $_tmp_csv_data = $row[$key];
                } else {
                    $_tmp_csv_data .= ',' . $row[$key];
                }

            }

            $csv_data .= $_tmp_csv_data . $row[$nums - 1] . "\r\n";
            unset($data[$k]);
        }

        $csv_data = mb_convert_encoding($csv_data, "cp936", "UTF-8");
        $file_name = empty($file_name) ? date('Y-m-d-H-i-s', time())
            : $file_name;
        // 解决IE浏览器输出中文名乱码的bug
        if (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
            $file_name = urlencode($file_name);
            $file_name = iconv('UTF-8', 'GBK//IGNORE', $file_name);
        }
        $file_name = $file_name . '.csv';
        header('Content-Type: application/download');
        header("Content-type:text/csv;");
        header("Content-Disposition:attachment;filename=" . $file_name);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $csv_data;
        exit();
    }

    /**
     * 判断是否为合法的身份证号码
     *
     * @param $mobile
     *
     * @return int
     */
    public static function isCreditNo ($vStr) {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91'
        );
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) {
            return false;
        }
        if (!in_array(substr($vStr, 0, 2), $vCity)) {
            return false;
        }
        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-'
                . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2)
                . '-' . substr($vStr, 10, 2);
        }
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
            return false;
        }
        if ($vLength == 18) {
            $vSum = 0;
            for ($i = 17; $i >= 0; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a')
                        ? 10
                        : intval(
                            $vSubStr, 11
                        ));
            }
            if ($vSum % 11 != 1) {
                return false;
            }
        }
        return true;
    }


    //二维数组排序
    public static function array_sort (
        $arrays, $sort_key, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC
    ) {
        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = $array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        return $arrays;
    }

    //搜索标记
    public static function search_sign ($word, $str) {
        if (empty($word) or empty($str)) {
            return false;
        }
        return preg_replace(
            "|($word)|Ui",
            "<span style=\"background:#FF913B;\"><b>$1</b></span>",
            $str
        );
    }

    //手机号码中间四位打*号
    public static function hidtel ($phone) {
        $IsWhat = preg_match(
            '/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone
        ); //固定电话
        if ($IsWhat == 1) {
            return preg_replace(
                '/(\d{3,4}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i',
                '$1****$2', $phone
            );
        } else {
            return preg_replace(
                '/(0?1[345678]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone
            );
        }
    }

    //判断手机号码
    public static function isMobile ($text) {
        $phone = '/^0?1[3|4|5|6|7|8][0-9]\d{8}\w?$/';
        $zuoji = '/^\d{3,4}-\d{7,8}\w?$/';
        if (preg_match($phone, $text) or preg_match($zuoji, $text)) {
            return (true);
        } else {
            return (false);
        }
    }

    //分享图缩略
    public static function processList ($url) {
        preg_match_all("/.*(\.jpg|\.png)/", $url, $arr);
        return $arr[0][0] . '?x-oss-process=list';
    }


    public static function processCove ($url) {
        preg_match_all("/.*(\.jpg|\.png)/", $url, $arr);
        return $arr[0][0] . '?x-oss-process=cover';
    }


    function base64url_encode ($data) {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'), '='
        );
    }

    function base64url_decode ($data) {
        return base64_decode(
            str_pad(
                strtr($data, '-_', '+/'), strlen($data) % 4, '=',
                STR_PAD_RIGHT
            )
        );
    }


    /**
     *URL生成二维码图片 返回OSS路径
     */
    public static function urlToImgUrl ($url, $filename = '') {
        $base64 = App::Qrcode($url);
        $base64 = substr($base64, strpos($base64, ',') + 1);
        $tmpFile = sys_get_temp_dir() . DS . uniqid() . '.png';
        $filename = $filename ?: date('Y/m/d/') . uniqid() . ".png";
        $file = fopen($tmpFile, "w");
        fwrite($file, base64_decode($base64));
        fclose($file);
        App::oss()->uploadFile(
            $GLOBALS['oss']['Bucket'], $filename, $tmpFile
        );
        unlink($tmpFile);
        return $GLOBALS['oss']['ossUrl'] . $filename;
    }

    /**
     * 判断金额类型
     * @param $money
     * @return bool
     */
    public static function isMoney ($money) {
        if (!is_numeric($money)) {
            return false;
        }
        if (doubleval($money) <= 0) {
            return false;
        }
        return true;
    }


    /**
     * 获取产品分享图
     *     array = [
     * background_img => 背景图 建议png 有默认值:http://oss.yiwane.com/2019/01/08/5c343f63291cd.png
     * avatar => 用户头像
     * product_img => 产品图片
     * name => 用户姓名
     * product_name =>产品名字 已做处理 字数超出没关系
     * first_price => 最低价
     * last_price => 最高价
     * //做生成二维码用
     * company_id => 公司ID
     * product_id => 产品ID
     * pshop_card_id => 用户ID
     * ]
     */
    public static function getProductShareImg ($array) {
        //wx头像处理成OSS路径
        $avatar = Common::wxDownloadFile($array['avatar']);
        $avatar = str_replace($GLOBALS['oss']['ossUrl'], '', $avatar);
        $header = $avatar . '?x-oss-process=image/resize,m_lfit,h_66,w_66/circle,r_100/format,png';
        $header = Common::base64url_encode($header);
        //背景图
        $img = $array['background_img'] ?: 'http://oss.yiwane.com/2019/01/08/5c343f63291cd.png';
        //产品图
        $centerImg = str_replace($GLOBALS['oss']['ossUrl'], '', $array['product_img']) .
            '?x-oss-process=image/resize,m_lfit,w_420';

        $centerImg = Common::base64url_encode($centerImg);
        //二维码
        $url = $GLOBALS['htmlUrl'] . '?company_id=' . $array['company_id'] .
            '&pgood_id=' . $array['product_id'] . '&pshop_card_id=' . $array['pshop_card_id'] . '#/gooddetail/';
        $qrCodeName = 'cpu/' . $array['company_id'] . '/' . $array['product_id'] . '/' . $array['pshop_card_id'] . '.png';
        $html = file_get_contents($GLOBALS['oss']['ossUrl'] . $qrCodeName . '?' .
                                  (App::oss())::OSS_PROCESS . '=image/info');
        if (!$html) {
            Common::urlToImgUrl($url, $qrCodeName);
        }
        $qrcode = $qrCodeName . '?x-oss-process=image/resize,h_86,w_86';
        $qrcode = Common::base64url_encode($qrcode);
        //用户名
        $array['name'] = $array['name']?:'*';
        $text = Common::base64url_encode($array['name']);
        //分享标题
        $text2 = Common::base64url_encode('“我发现了好物，一起来看看吧”');
        //产品名
        $product_name = $array['product_name'];
        $num = mb_strlen($product_name, "utf-8");
        $str4 = '';
        if ($num > 16) {
            $text3 = mb_substr($product_name, 0, 16);

            $text3 = Common::base64url_encode($text3);
            if ($num > 30) {
                $text4 = mb_substr($product_name, 17, 14) . '...';
            } else {
                $text4 = mb_substr($product_name, 17);
            }

            $text4 = Common::base64url_encode($text4);
            $str4 = "/watermark,type_d3F5LXplbmhlaQ,text_{$text4},size_16,t_100,g_sw,x_30,y_70";
        }else{
            $text3 = Common::base64url_encode($product_name);
        }

        //价格
        $text5 = "￥{$array['first_price']}~{$array['last_price']}";
        if($array['first_price'] ==  $array['last_price']){
            $text5 = "￥{$array['first_price']}";
        }
        $text5 = Common::base64url_encode($text5);


        return $img . "?x-oss-process=image/rounded-corners,r_5" .
            "/watermark,image_{$header},t_90,g_nw,x_30,y_28" .
            "/watermark,image_{$qrcode},t_100,g_se,x_36,y_30"
           . "/watermark,type_d3F5LXplbmhlaQ,text_{$text},color_c2c2c2,size_16,t_100,g_nw,x_118,y_42,shadow_90"
           . "/watermark,type_ZmFuZ3poZW5nc2h1c29uZw,text_{$text2},size_16,t_100,g_nw,x_118,y_70"
            ."/watermark,type_d3F5LXplbmhlaQ,text_{$text3},size_16,t_100,g_sw,x_30,y_98"
           . $str4
            ."/watermark,type_d3F5LXplbmhlaQ,text_{$text5},color_26a2ff,size_16,t_100,g_sw,x_30,y_30"
           . "/watermark,image_{$centerImg},t_100,g_center"
        ;
    }


}