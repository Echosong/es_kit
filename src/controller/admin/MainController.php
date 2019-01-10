<?php

/**
 * 后台首页，文章管理
 * User: echosong
 * Date: 2017/8/15
 * Time: 14:34
 */
class MainController extends BaseController
{
    public function getMain()
    {
        $this->layout = null;
        $this->display();
    }

    public function getIndex()
    {
        $this->display();
    }


}