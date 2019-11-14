<?php


namespace app\api\library;

use app\api\controller\Base;
use app\api\model\Config as ConfigModel;
use EasyWeChat\Factory;

class WeChat
{
    private $AppID; // AppID

    private $AppSecret; // AppSecret

    private $app; // 小程序实例

    public function __construct()
    {
        $this->AppID = (ConfigModel::get(['name' => 'AppID']))['value']; // AppID
        $this->AppSecret = (ConfigModel::get(['name' => 'AppSecret']))['value']; // AppSecret
        $this->checkWeChatConfig(); // 校验后台是否配置微信小程序 AppID和AppSecret

        $config = [
            'app_id' => $this->AppID,
            'secret' => $this->AppSecret,
        ];

        // 实例话小程序
        $this->app = Factory::miniProgram($config);
    }

    /**
     * 登录小程序 获取 openID 和 SessionKey
     * @param $code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function miniProgram($code)
    {
        $result = $this->app->auth->session($code);
        return $result;
    }

    /**
     * 加密数据解密
     * @param $sessionKey
     * @param $iv
     * @param $encryptedData
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function decryptData($sessionKey, $iv, $encryptedData)
    {
        $result = $this->app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
        return $result;
    }

    public function getMPCode($url, $id)
    {
        $response = $this->app->app_code->getUnlimit('id=' . $id, [
            'page' => $url,
            'width' => 500,
        ]);

        $name = session_create_id();
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            return $filename = $response->save(ROOT_PATH . 'public/wechat', $name . '.png');
        }
    }

    private function checkWeChatConfig()
    {
        if (empty($this->AppID)) {
            Base::weChatException('请在后台配置小程序的AppID');
        }
        if (empty($this->AppSecret)) {
            Base::weChatException('请在后台配置小程序的AppSecret');
        }
        return true;
    }
}