<?php


namespace app\api\model;


use think\Model;

class User extends Model
{
    // 表名
    protected $name = 'user';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function candidate()
    {
        return $this->belongsTo('candidate', 'candidate_id', 'id');
    }
}