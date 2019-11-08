<?php


namespace app\api\library;


use app\api\controller\Base;
use think\Cache;
use think\Request;

class Token extends Base
{
    public function hasToken(Request $request)
    {
        // 验证是否传递 access_token
        $header = $request->header();
        $result = parent::validate($header, 'TokenValidate');

        if ($result !== true) {
            parent::error('error', $result, 400, 'json'); // 未传递 code
        }

        $cache = $this->check($header['token']);

        return $cache;
    }

    private function check($token)
    {
        $cache = Cache::get($token);
        if (!$cache) {
            parent::error('authorization', '验证失败请登录', 401, 'json');
        }

        return $cache;
    }
}