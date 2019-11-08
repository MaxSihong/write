<?php


namespace app\api\library;


use Endroid\QrCode\QrCode;
use think\Request;

class QRCodeEN
{
    public function getCode($data)
    {
        $path = Request::instance()->domain();
        // 创建基本二维码
        $qrCode = new QrCode($data);
        $qrCode->setSize(300); // 尺寸
        $file_name = md5(microtime(true) . time()); // 文件名称

        $qrCode->writeFile(ROOT_PATH . 'public/qrcode' . DS . $file_name . '.png'); // 保存二维码图片
        return $path . '/qrcode/' . $file_name . '.png';
    }
}