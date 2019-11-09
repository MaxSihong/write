<?php


namespace app\api\validate;


use think\Validate;

class UserInfoValidate extends Validate
{
    protected $rule = [
        'name|姓名' => 'require',
        'phone|手机号' => 'require|isMobile',
        'judges｜对评选人的期望' => 'require|max:100',
        'children|对孩子的期望' => 'require|max:100',
    ];

    // 手机号验证
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}