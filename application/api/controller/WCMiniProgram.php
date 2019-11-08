<?php


namespace app\api\controller;


use app\api\library\WeChat;

class WCMiniProgram extends Base
{
    public function getMPCode()
    {
        $url = 'pages/boost/boost';
        (new WeChat())->getMPCode($url);
    }
}