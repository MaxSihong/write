<?php


namespace app\api\controller\user;

use app\api\library\Token;
use app\api\model\User as UserModel;
use app\api\model\Config as ConfigModel;
use app\api\model\UserInfo as UserInfoModel;
use app\api\model\Candidate as CandidateModel;
use app\api\controller\Base;
use think\Db;
use think\Request;

class User extends Base
{
    /**
     * 我的
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function mine()
    {
        $cache = (new Token())->hasToken(Request());

        $user_info = (new UserModel())->where('id', $cache['id'])
            ->field('id,name,avatar_url,voted,frequency,candidate_id')
            ->find();

        if ($user_info['candidate_id'] == null) { // 不是考生，则不需要排名
            $data = $user_info;
        } else {
            $result = (new UserModel())->where('candidate_id', '<>', 'null')
                ->order('number', 'desc')
                ->field('id,name,avatar_url,voted,frequency,candidate_id,number')
                ->select();

            // 排名
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['ranking'] = $i + 1;
            }

            $data = [];
            foreach ($result as $key => $value) {
                if ($value['id'] == $cache['id']) {
                    unset($value['number']);
                    $data = $value;
                }
            }
        }

        parent::success('success', $data, 200, 'json');
    }

    /**
     * 学生认证
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function studentCertification()
    {
        $cache = (new Token())->hasToken(Request());

        $data = Request::instance()->post();

        $result = parent::validate($data, 'StudentCertificationValidate');
        if ($result !== true) {
            parent::error('error', $result, 400, 'json');
        }

        $candidate = (new CandidateModel())->where('name', $data['name']) // 姓名
            ->where('candidate_number', $data['candidate_number']) // 考号
            ->find();
        if (!$result) {
            parent::error('error', '信息错误请重试', 400, 'json');
        }

        if ($candidate['user_id'] !== null) {
            parent::error('error', '不能重复认证', 400, 'json');
        }

        // 保存手机号 并根user关联
        $result = (new CandidateModel())->save([
            'phone' => $data['phone'],
            'user_id' => $cache['id']
        ], ['id' => $candidate['id']]);

        // 考生根用户关联
        $result = (new UserModel())->save([
            'name' => $data['name'],
            'candidate_id' => $candidate['id'],
            'candidate_number' => $candidate['candidate_number'],
        ], ['id' => $cache['id']]);

        parent::success('success', '认证成功', 200, 'json');
    }

    /**
     * 填写完整信息
     */
    public function addUserInfo()
    {
        $cache = (new Token())->hasToken(Request());

        $data = Request::instance()->post();
        $result = parent::validate($data, 'UserInfoValidate');
        if ($result !== true) {
            parent::error('error', $result, 400, 'json');
        }

        $data['user_id'] = $cache['id'];

        (new UserInfoModel())->allowField(true)->save($data); // 保存用户完整信息
        $user = UserInfoModel::get(['user_id' => $cache['id']]);
        $user_info = UserModel::get($cache['id']);

        if ($user_info['candidate_id'] == null) { //不是考生
            // 更新用户信息完整状态 投票次数 姓名 手机号
            Db::name('user')->where('id', $cache['id'])
                ->update([
                    'is_complete' => 1,
                    'frequency' => Db::raw('frequency+' . 2),
                    'phone' => $data['phone'],
                    'user_info_id' => $user['id'],
                ]);
        } else { // 考生
            // 更新用户信息完整状态 投票次数 姓名 手机号
            Db::name('user')->where('id', $cache['id'])
                ->update([
                    'is_complete' => 1,
                    'frequency' => Db::raw('frequency+' . 2),
                    'user_info_id' => $user['id'],
                ]);
        }

        parent::success('success', '保存成功', 200, 'json');
    }

    /**
     * 签到
     * @throws \think\exception\DbException
     */
    public function check()
    {
        $cache = (new Token())->hasToken(Request());

        $check_time = (ConfigModel::get(['name' => 'check_time']))['value']; // 签到开始时间
        $end_time = (ConfigModel::get(['name' => 'end_time']))['value']; // 签到结束时间
        $date = date('Y-m-d H:i:s'); // 现在时间

        if ($date < $check_time) {
            parent::error('error', '签到还未开始', 400, 'json');
        }
        if ($date > $end_time) {
            parent::error('error', '签到已结束', 400, 'json');
        }

        $user = UserModel::get($cache['id']);
        $result = $user->candidate->is_check;
        if ($result == 1) {
            parent::error('error', '您已签到', 400, 'json');
        }

        $user->candidate->save(['is_check' => 1]);

        parent::success('success', '签到成功', 200, 'json');
    }

    /**
     * 获取座位号等信息
     * @throws \think\exception\DbException
     */
    public function candidate()
    {
        $cache = (new Token())->hasToken(Request());

        $user_info = UserModel::get(['id' => $cache['id']]);
        $candidate = CandidateModel::get(['id' => $user_info['candidate_id']]);

        parent::success('success', $candidate, 200, 'json');
    }
}