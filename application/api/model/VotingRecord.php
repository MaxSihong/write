<?php


namespace app\api\model;


use think\Model;

class VotingRecord extends Model
{
    // 表名
    protected $name = 'voting_record';

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
        return $this->belongsTo('Candidate', 'voted_id', 'id')
            ->bind('name');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id')
            ->bind('name');
    }
}