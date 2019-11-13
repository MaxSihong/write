<?php


namespace app\api\model;


use think\Model;
use think\Request;

class Candidate extends Model
{
    // 表名
    protected $name = 'candidate';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id')
            ->find('id,name,avatar_url,voted,frequency,candidate_id,number');
    }

    // 图片拼接
    public function getAvatarUrlAttr($value)
    {
        if (strstr($value, 'https://wx.qlogo.cn')) {
            return $value;
        }
        $root_path = Request::instance()->domain(); // 获取当前域名
        return $root_path . $value;
    }
}