<?php


namespace app\api\validate;


use think\Validate;

class UserInfoValidate extends Validate
{
    protected $rule = [
        'name|姓名' => 'require',
        'phone|手机号' => 'require',
        'judges｜对评选人的期望' => 'require|max:100',
        'children|对孩子的期望' => 'require|max:100',
    ];
}