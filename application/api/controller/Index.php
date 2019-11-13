<?php


namespace app\api\controller;

use app\api\model\Candidate as CandidateModel;
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
        $data = (new CandidateModel())->where(null)
            ->order('number', 'desc')
            ->field('id,name,number,candidate_number,avatar_url')
            ->limit('10')
            ->select();

        // 排名
        for ($i = 0; $i < count($data); $i++) {
            if ($i < 3) {
                $data[$i]['status'] = $i + 1;
            }
            $data[$i]['ranking'] = $i + 1;
        }

        foreach ($data as $key => $value) {
            $value['candidate_number'] = self::func_substr_replace($value['candidate_number']);
        }

        $root = Request::instance()->domain();
        $new_data = [
            'user_info' => $data,
            'background_music' => $root . '/music/background.mp3',
        ];

        parent::success('success', $new_data, 200, 'json');
    }

    public static function func_substr_replace($str, $replacement = '*', $start = 2, $length = 3)
    {
        $len = mb_strlen($str, 'utf-8');

        if ($len > intval($start + $length)) {
            $str1 = mb_substr($str, 0, $start, 'utf-8');
            $str2 = mb_substr($str, intval($start + $length), NULL, 'utf-8');
        } else {
            $str1 = mb_substr($str, 0, 1, 'utf-8');
            $str2 = mb_substr($str, $len - 1, 1, 'utf-8');
            $length = $len - 2;

        }
        $new_str = $str1;

        for ($i = 0; $i < $length; $i++) {

            $new_str .= $replacement;

        }
        $new_str .= $str2;
        return $new_str;
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