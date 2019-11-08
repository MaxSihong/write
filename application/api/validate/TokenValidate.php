<?php


namespace app\api\validate;


use think\Validate;

class TokenValidate extends Validate
{
    protected $rule = [
        'token' => 'require',
    ];

    protected $message = [
        'token' => '验证失败请登录',
    ];
}