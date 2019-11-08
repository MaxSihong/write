<?php


namespace app\api\validate;


use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'code' => 'require',
    ];
}