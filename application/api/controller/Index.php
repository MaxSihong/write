<?php


namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\model\Config as ConfigModel;
use think\Request;

class Index extends Base
{
    /**
     * 首页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data = (new UserModel())->where('candidate_id', '<>', 'null')
            ->order('number', 'desc')
            ->field('id,name,number,candidate_number')
            ->limit('10')
            ->select();

        // 排名
        for ($i = 0; $i < count($data); $i++) {
            if ($i + 1 < 3) {
                $data[$i]['status'] = $i + 1;
            }
            $data[$i]['ranking'] = $i + 1;
        }

        $root = Request::instance()->domain();
        $new_data = [
            'user_info' => $data,
            'background_music' => $root . '/music/background.mp3',
        ];

        parent::success('success', $new_data, 200, 'json');
    }

    /**
     * 获取投票开始和结束时间
     * @throws \think\exception\DbException
     */
    public function getTime()
    {
        $start_time = (ConfigModel::get(['name' => 'check_time']))['value'];
        $end_time = (ConfigModel::get(['name' => 'end_time']))['value'];

        $data = [
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];

        parent::success('success', $data, 200, 'json');
    }
}