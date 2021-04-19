<?php
/**
 * @title 登录注册
 * Class Api
 */
class Api
{
    /**
     * @title 用户登录API
     * @url https://wwww.baidu.com/login
     * @method POST
     * @param string username 是 账号 无 无 账户登陆信息
     * @param string password 是 密码 无 无 账户登陆信息
     * @code 1 成功
     * @code 2 失败
     * @desc_return int code 是 状态码（具体参见状态码说明） 无 无
     * @desc_return string msg 是 提示信息 无 无
     */
    public function login() {
        return json_encode(['code' => 1, 'msg' => '登录成功']);
    }
}
