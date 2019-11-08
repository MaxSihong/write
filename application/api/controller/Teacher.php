<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Db;

class Teacher extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function show($id)
    {
        $data =Db::name('teacher')->findOrFail($id);
        $this->success('ok',$data,200);
    }
}