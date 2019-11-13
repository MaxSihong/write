<?php


namespace app\api\controller;

use app\api\library\Token;
use app\api\model\User as UserModel;
use app\api\model\VotingRecord as VotingRecordModel;

class VotingRecord extends Base
{
    /**
     * 获票记录 和 投票记录 0 1
     * @param $status
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($status)
    {
        $cache = (new Token())->hasToken(Request());

        if ($status == 0) { // 获票记录
            $user_info = UserModel::get($cache['id']);
            if ($user_info['candidate_id'] !== null) {
                $data = (new VotingRecordModel())->where('voted_id', $user_info['candidate_id'])
                    ->with(['user'])
                    ->select();
            } else {
                $data = [];
            }
        } elseif ($status == 1) { // 投票记录
            $data = (new VotingRecordModel())->where('user_id', $cache['id'])
                ->with(['candidate'])
                ->select();
        }

        foreach ($data as $value) {
            unset($value['user_id'], $value['voted_id']);
        }

        parent::success('success', $data, 200, 'json');
    }
}