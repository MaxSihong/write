<?php


namespace app\api\controller;


use app\api\library\QRCodeEN;
use app\api\library\WeChat;

class WCMiniProgram extends Base
{
    public function getMPCode()
    {
        $data = [
            'status' => '签到'
        ];
        return (new QRCodeEN())->getCode(json_encode($data));
    }
}