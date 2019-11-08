<?php

namespace app\admin\model;

use think\Model;


class Bag extends Model
{


    // 表名
    protected $name = 'bag';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function codes()
    {
        return $this->hasMany(Code::class);
    }

    public function writes()
    {
        return $this->hasMany(Write::class);
    }

}
