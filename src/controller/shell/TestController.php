<?php

/**
 * 测试脚本案例
 * User: echosong
 * Date: 2017/9/11
 * Time: 11:06
 */
class TestController extends BaseController
{

    private function filter(&$row)
    {
        $row[0] = 5;
    }

    function mail_utf8($to, $from_user, $from_email,
        $subject = '(No subject)', $message = '')
    {
        $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
        $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
        $headers = "From: $from_user <$from_email>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";

        return mail($to, $subject, $message, $headers);
    }





    public function actionSendMail()
    {
        $to = 'all@dianbaobao.com';
        $subject = '联系我';

        $message = '
<html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh">
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<body>
  速度，企业qq上联系我  。
</body>
</html>
';
       $flg = $this->mail_utf8($to,'1', '1@dianbaobao.com', $subject, $message);
       if($flg){
           echo "发送成功";
       }else{
           echo "发送失败";
       }

    }

    public function actionIndex()
    {
        $oderDb = new Model('order');
        $oderDb->update(['id'=>1],['+book_num'=>1]);
    }

    /*
     * http://es.dev:801/shell/test/createapi
     * */
    public function actionCreateApi()
    {
        $dir = APP_DIR . '/apidocs/';
        $url = '/apidocs/';
        $fp = fopen($dir . "index.html", "w");
        fwrite($fp,
            '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<meta name="description" content="php-apidoc - apid documenation generator">'
            . '<meta name="author" content="Calin Rada">'
            . '<title>UserControllerTitle</title>'
            . '<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">'
            . '<style type="text/css">'
            . '  body     { padding-top: 70px; margin-bottom: 15px; '
            . ' .tab-pane { padding-top: 10px; }'
            . ' .mt0      { margin-top: 0px; }'
            . ' .footer   { font-size: 12px; color: #666; }'
            . ' .label    { display: inline-block; min-width: 65px; padding: 0.3em 0.6em 0.3em; }'
            . ' .string   { color: green; }'
            . ' .number   { color: darkorange; }'
            . ' .boolean  { color: blue; }'
            . ' .null     { color: magenta; }'
            . ' .key      { color: red; }'
            . ' .popover  { max-width: 400px; max-height: 400px; overflow-y: auto;}'
            . ' </style>'
            . ' </head ><body><div class="navbar navbar-default navbar-fixed-top" role="navigation"> </div>'
            . ' <div class="container"><div class="panel-group" id="accordion">');
        $files = scandir($dir);
        foreach ($files as $k => $v) {
            if (strlen($v) > 3 && $v != 'index.html') {
                $demo = '<div class="panel panel-default">'
                    . '<div class="panel-heading">'
                    . ' <h4 class="panel-title">'
                    . ' <a data-toggle="collapse" data-parent="#accordion1" href = "'
                    . $url . $v . '" > <span class="label label-primary">'
                    . $v . '</span></a>'
                    . '</h4>'
                    . '</div>'
                    . ' </div> ';
                fwrite($fp, $demo);
            }
        }
        fwrite($fp, "</div> </div> </body></html>");
        fclose($fp);
        header("Location: " . $url . 'index.html');
    }

    public function actionTest(){

        App::sendSMS("","",[]);

        $smsDb = new Sms();
        if ($smsDb->sendsms('18037701800', Sms::SMSTEMPLATE, '123123', ['code'=>'123123'])) {
            Helper::responseJson('已发送验证码！');
        } else {
            Helper::responseJson('验证码发送失败！', 1);
        }
        //var_dump($this->encryptDesEcbPKCS5('18818869027','C9eLew12'));
    }


}