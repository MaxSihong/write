<?php


namespace app\api\controller;


use app\common\controller\Api;

class Base extends Api
{
    protected $noNeedLogin = ['*'];

    public function __construct()
    {
        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }
    }
}