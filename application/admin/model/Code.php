<?php


namespace app\admin\model;


use think\Model;

class Code extends Model
{
    protected $name = 'code';

    public function bag()
    {
        return $this->belongsTo(Bag::class);
    }
}