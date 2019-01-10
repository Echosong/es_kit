<?php

class User extends Model
{
    //表名
    public $table_name = 'user';


    const ENABLED = 1;
    const DISABLED = 0;

    const  LOGINBIND = 1;

    const LOGININ = 2;

    public static $state = [
        'ADD_SCENE_SEARCH'=>'公众号搜索',
        'ADD_SCENE_ACCOUNT_MIGRATION'=>'公众号迁移',
        'ADD_SCENE_PROFILE_CARD'=>'名片分享',
        'ADD_SCENE_QR_CODE'=>'扫描二维码',
        'ADD_SCENEPROFILE_LINK'=>'图文页内名称点击',
        'ADD_SCENE_PROFILE_ITEM'=>'图文页右上角菜单',
        'ADD_SCENE_PAID'=>'支付后关注',
        'ADD_SCENE_OTHERS'=>'其他',
        'UNSUBSCRIBE'=>'取消关注',
        'AD_ARTICLE'=>'公众号文章广告',
        'AD_FRIENDS'=>'朋友圈广告'
    ];

    const SEX_UNKNOW = 0;
    const SEX_MAN = 1;
    const SEX_WOMAN = 2;

    public static $sex = [
        self::SEX_UNKNOW => '保密',
        self::SEX_MAN => '男',
        self::SEX_WOMAN => '女',
    ];

    const ADMIN = 0;
    const COMPANY = 1;
    const USER = 2;
    const CHU = 3;

    public static $level = [
        self::ADMIN => '管理员',
        self::COMPANY => '企业管理',
        self::USER => '普通员工',
        self::CHU => '厨房管理'
    ];

    //验证字段规则
    public static $rules = [
        'name' => [['lengthMax', 30]],
        'username' => [['lengthMax', 15]],
        'nick' => [['lengthMax', 20]],
        'avatar' => [['lengthMax', 200]],
        'last_time' => ['date'],
        'last_ip' => [['lengthMax', 20]],
        'level' => ['integer', ['max', 127]],
        'sex' => ['integer', ['max', 127]],
        'state' => ['integer', ['max', 127]],
        'reg_ip' => [['lengthMax', 20]],
        'password_sc' => [['lengthMax', 32]],
        'mac' => [['lengthMax', 200]],
        'company' => [['lengthMax', 50]],
        'company_id' => ['integer', ['max', 4294967295]],
        'number' => [['lengthMax', 50]],
        'join_time' => ['date']
        ];

    //数据库字段
    public $fields = ['id','name','username','password','nick','avatar','last_time','last_ip','level','created','updated','sex','state','reg_ip','password_sc','mac','company','company_id','address','wx','qq','remark','info','id_card','iscard','father_id','grand_father_id','birthday','openid','location','subscribe_time','staff_id'];

    /**处理前后台登录公用
     * @param $username
     * @param $password
     * @param $token 硬件信息
     * @param $module 默认后台登录
     * @return int
     */
    public function login ($username, $password, $token = "", $module = "admin") {
        if ($module == 'admin') {
            $where = ["username = :username and level <= :type", [':username' => $username, ':type' => self::COMPANY]];
            $user = $this->find($where);
            if (empty($user)) {
                return ['code' => 1, "message" => "账号或者密码错误"];
            } else {
                if ($user['state'] == self::DISABLED) {
                    return ['code' => 3, 'message' => '账号已被禁用'];
                }
                $code = $_SESSION['admin_code'];

                $pwd = openssl_decrypt(base64_decode($password), 'des-ecb', $code, true);
                if (!password_verify($pwd, $user['password'])) {
                    Helper::responseJson('密码或者账号错误！', 4);
                }

                $this->update(['id' => $user['id']],
                              [
                                  'last_time' => date('Y-m-d H:i:s'),
                                  'last_ip' => Helper::userIp()
                              ]
                );
            }
            return ['code' => 0, 'message' => $user];
        }
    }

    public function loginLog ($data) {
        $this->update(['id' => $data['userid']],
                      ['last_ip' => $data['ip'], 'last_time' => date('Y-m-d H:i:s'), 'mac' => $data['mac']]);
        $operate = new OperateLog();
        $operate->insert(
            [
                'event' => OperateLog::LOGIN,
                'create_id' => $data['userid'],
                'client_ip' => $data['ip'],
                'info' => $data['info'],
                'server_ip' => $_SERVER['SERVER_ADDR'],
                'username' => $data['username']
            ]
        );
    }

    /**
     * 插入用户信息
     * @param $rows
     * @return array|bool|mixed
     */
    public function insert ($rows) {
        //为了分销
        if (empty($rows['parent_id'])) {
            $rows['parent_id'] = null;
        } else {
            $grandpa = $this->find(['id' => $rows['parent_id']], 'id desc', 'parent_id');
            $rows['grandpa_id'] = $grandpa['parent_id'];
        }
        $user = [
            'username' => $rows['mobile']? $rows['mobile']: $rows['username'],
            'state' => 1,
            'password' => password_hash($rows['id'], PASSWORD_DEFAULT),
            'reg_ip' => Helper::userIp(),
            'last_time' => date("Y-m-d H:i:s"),
            'type' => 0,
            'mac' => $rows['mac'],
            'avatar' => $rows['avatar'],
            'company_id' => $rows['company_id'],
            'company' => $rows['company'],
            'nick' => $rows['nick'],
            'openid'=>$rows['openid'],
            'last_ip'=>Helper::userIp(),
            'name'=> $rows['name'],
            'sex'=>$rows['sex']?$rows['sex']:0
        ];
        if($rows['iscard']){
            $user['iscard'] = 1;
        }
        foreach ($user as $k=>$v){
            if(!isset($v)){
                unset($user[$k]);
            }
        }
        //先判断用户
        $inUser = $this->find(['openid' => $rows['openid']]);
        if ($inUser) {
            if(!Common::isMobile($user['username'])){
                unset($user['username']);
            }
            $this->update(['id'=>$inUser['id']],$user);
            $inUser['userid'] = $inUser['id'];
            unset($inUser['id']);
            return $inUser;
        }
        $id = $this->create($user);
        $user['userid'] = $id;
        return $user;
    }


    public function index ($id) {
        $cuser = $this->find(['id' => $id]);
        $userExtDb = new UserExtend();
        $userEx = $userExtDb->find(['userid' => $id]);
        $cuser['ext'] = $userEx;
        $balanceDb = new UserBalance();
        $balance = $balanceDb->find(['userid' => $id]);
        $cuser['balance'] = $balance;
        unset($cuser['password']);
        unset($cuser['password_sec']);
        unset($cuser['token']);
        unset($cuser['token_update']);
        return $cuser;
    }


}
