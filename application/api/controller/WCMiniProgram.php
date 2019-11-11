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

    public function getWeChatCode($id)
    {
        $url = 'pages/PullTicket/PullTicket';
        $result = (new WeChat())->getMPCode($url, $id);
        return json_encode($result);
    }
}