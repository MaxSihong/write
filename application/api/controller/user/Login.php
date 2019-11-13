<?php


namespace app\api\controller\user;

use app\api\controller\Base;
use app\api\controller\Common;
use app\api\library\WeChat;
use app\api\model\User as UserModel;
use think\Cache;
use think\Request;

class Login extends Base
{
    /**
     * 小程序登录
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $data = $this->checkData(Request()); // 校验参数 获取code

        $result = (new WeChat())->miniProgram($data['code']); // 获取 OpenID 和 SessionKey
        $user_info = $this->checkOpenID($result, $data); // 查看该用户是否登录过小程序
        $user_info = UserModel::get(['openId' => $result['openid']]);

        $result['id'] = $user_info['id'];

        $token = $this->generateToken(); // 生成 Token
        $date = 60 * 60 * 24 * 30; // 单位：秒 30天
        Cache::set($token, $result, $date); // 写入缓存

        $data = [
            'token' => $token, // token
        ];

        parent::success('success', $data, 200, 'json');
    }

    // 验证openId是否已经存在，不存在则保存，存在则直接返回
    private function checkOpenID($result, $data)
    {
        $user = UserModel::get(['openId' => $result['openid']]);

        if (empty($user)) {
            $user_info = (new WeChat())->decryptData($result['session_key'], $data['iv'], $data['encryptedData']); // 获取用户信息

            // 保存用户openid 和 头像
            $result = (new UserModel())->save([
                'openid' => $result['openid'],
                'avatar_url' => $user_info['avatarUrl'],
                'name' => $user_info['nickName'],
            ]);
            if (!$result) {
                parent::error('error', '服务器繁忙，请稍后再试', 500, 'json');
            }
        }
        return true;
    }

    // 生成 Token
    private function generateToken()
    {
        // 32个字符组成一组随机字符串
        $randChars = Common::getRandChar(32);
        // 用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars . $timestamp . $salt);
    }

    // 参数校验
    private function checkData(Request $request)
    {
        // 验证是否传递需要的参数
        $data = $request->post();
        $result = parent::validate($data, 'LoginValidate');

        if ($result !== true) {
            parent::error('error', $result, '400', 'json'); // 未传递 code
        }

        return $data;
    }
}