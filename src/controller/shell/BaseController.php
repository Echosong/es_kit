<?php

/**
 * Created by PhpStorm.
 * User: echosong
 * Date: 2017/9/11
 * Time: 11:05
 */
class BaseController extends Controller
{
    //未登录或者登陆超时 100-200 为常量code
    const CODE_USER_TIMEOUT = 100;

    //前端参数验证错误
    const CODE_USER_PARAM_ERROR = 101;

    //第三方授权第一次
    const CODE_USER_OATH_FIRST = 102;
}