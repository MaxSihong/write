<?php


namespace app\api\validate;


use think\Validate;

class StudentCertificationValidate extends Validate
{
    protected $rule = [
        'name|姓名' => 'require|length:2,50',
        'phone|手机号' => 'require|isMobile',
        'candidate_number|考号' => 'require',
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