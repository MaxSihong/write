<?php


namespace app\api\controller\user;

use app\api\controller\Base;
use app\api\controller\Index;
use app\api\library\Token;
use app\api\library\WeChat;
use app\api\model\Candidate as CandidateModel;
use app\api\model\User as UserModel;
use app\api\model\VotingRecord as VotingRecordModel;
use think\Db;
use think\Request;

class Vote extends Base
{
    /**
     * 拉票
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $token = (new Token())->hasToken(Request());

        $user_info = (new UserModel())->where('id', $token['id'])
            ->field('id,avatar_url,number,candidate_id')
            ->find();

        if ($user_info['candidate_id'] == null) {
            parent::error('error', '您没有权限访问', 403, 'json');
        }

        $root = Request::instance()->domain();
        $user_info['background_music'] = $root . '/music/background.mp3';


        $path = Request::instance()->domain();
        $url = 'packageA/pages/boost/boost';
        $result = (new WeChat())->getMPCode($url, $user_info['candidate_id']);
        $result_url = $path . '/wechat/' . $result;
        $user_info['qrcode_url'] = $result_url; // 生成二维码

        parent::success('success', $user_info, 200, 'json');
    }

    /**
     * 助力页面
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get($id)
    {
        $data = (new CandidateModel())->where(null)
            ->order('number', 'desc')
            ->field('id,name,number,candidate_number,avatar_url')
            ->select();

        // 排名
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['ranking'] = $i + 1;
        }

        $new_data = [];
        foreach ($data as $key => $value) {
            if ($value['id'] == $id) {
                $new_data = $value;
            }
        }

        if (empty($new_data)) {
            parent::error('error', '服务器繁忙，请稍后再试', 500, 'json');
        }

        $root = Request::instance()->domain();
        $new_data['candidate_number'] = Index::func_substr_replace($new_data['candidate_number']);
        $new_data['background_music'] = $root . '/music/background.mp3';

        parent::success('success', $new_data, 200, 'json');
    }

    /**
     * 投票
     * @param $id
     * @throws \think\exception\DbException
     */
    public function boost($id)
    {
        /**
         * TODO: 投票
         * 1、Token验证
         * 2、验证穿的用户id是否是考生
         * 2、查询该用户当天是否有投票，如果没有则为该用户重置投票次数
         * 3、当天如果有投票，则判断他是否还拥有投票次数
         * 4、没有投票机会，返回投票失败状态给前端
         * 5、投票成功，为投票用户减少投票次数, 新增已投票次数，新增投票记录，新增被投票用户的总票数
         * 6、返回成功状态 剩余投票次数 是否填写完整信息 （is_complete）
         */
        $cache = (new Token())->hasToken(Request());

        $user_info = UserModel::get($cache['id']); // 获取用户信息

        $result = $this->checkUserDateHasFrequency($cache); // 查看当天是否有投票 false 未投票 true 有投票
        if (!$result) {
            $user_info = $this->putUserFrequency($user_info); // 更改用户的投票次数
        } else {
            $result = $this->checkHasFrequency($user_info); // 检测该用户是否还有投票次数
            if ($result === 'noData') { // 如果该用户没有投票机会，并且未填写完整信息
                $user = UserModel::get($user_info['id']);
                $data = [
                    'frequency' => $user['frequency'], // 剩余投票次数
                    'is_complete' => $user['is_complete'], // 是否填写完整信息
                    'status' => 1
                ];
                parent::success('success', $data, 200, 'json');
            } elseif ($result === 'noData2') {
                $user = UserModel::get($user_info['id']);
                $data = [
                    'frequency' => $user['frequency'], // 剩余投票次数
                    'is_complete' => $user['is_complete'], // 是否填写完整信息
                    'status' => 2
                ];
                parent::success('success', $data, 200, 'json');
            } elseif ($result === 'no') { // 如果该用户没有投票机会，但是填写完整信息
                $user = UserModel::get($user_info['id']);
                $data = [
                    'frequency' => $user['frequency'], // 剩余投票次数
                    'is_complete' => $user['is_complete'], // 是否填写完整信息
                ];
                parent::success('success', $data, 200, 'json');
            }
        }

        $result = $this->successfulVote($id, $user_info); // 投票成功 减少投票次数，新增已投票次数，新增投票记录

        $user = UserModel::get($user_info['id']);
        $data = [
            'frequency' => $user['frequency'], // 剩余投票次数
            'is_complete' => $user['is_complete'] // 是否填写完整信息
        ];

        parent::success('success', $data, 200, 'json');
    }

    // 投票成功 减少投票次数，新增已投票次数，新增投票记录，新增被投票用户的总票数
    private function successfulVote($id, $user_info)
    {
        // 减少投票次数 新增已投票次数
        $result = Db::name('user')->where('id', $user_info['id'])
            ->update([
                'frequency' => Db::raw('frequency-' . 1), // 减少投票次数
                'voted' => Db::raw('voted+' . 1), // 新增已投票次数
            ]);
        if (!$result) {
            return false;
        }

        $result = $this->addVotingRecord($id, $user_info); // 新增投票记录
        if (!$result) {
            return false;
        }

        $result = $this->addUserNumber($id); // 新增被投票用户的总票数
        if (!$result) {
            return false;
        }

        return true;
    }

    // 新增被投票用户的总票数
    private function addUserNumber($id)
    {
        $result = Db::name('candidate')->where('id', $id)
            ->update([
                'number' => Db::raw('number+' . 1),
            ]);
        $user_info = CandidateModel::get($id);
        if ($user_info['user_id'] !== null) {
            $result = Db::name('user')->where('id', $user_info['user_id'])
                ->update([
                    'number' => Db::raw('number+' . 1),
                ]);
        }
        if (!$result) {
            return false;
        }
        return true;
    }

    // 新增投票记录
    private function addVotingRecord($id, $user_info)
    {
        $date = date('Y-m-d'); // 当前投票日期

        $data = [
            'user_id' => $user_info['id'],
            'voted_id' => $id, // 考生表考生ID
            'voted' => $date
        ];
        $result = (new VotingRecordModel())->save($data);
        if (!$result) {
            return false;
        }

        return true;
    }

    // 检测该用户是否还有投票次数
    private function checkHasFrequency($user_info)
    {
        if ($user_info['frequency'] == 0 && $user_info['is_complete'] == 0) {
            return 'noData';
        }
        if ($user_info['frequency'] == 0 && $user_info['is_complete'] == 1) {
            return 'noData2';
        }
        if ($user_info['frequency'] == 0) {
            return 'no';
        }

        return true;
    }

    // 更改用户的投票次数
    private function putUserFrequency($user_info)
    {
        if ($user_info['is_complete'] == 0) { // 未填写完整信息，有 1 次投票次数
            $result = (new UserModel())->save([
                'frequency' => 1,
            ], ['id' => $user_info['id']]);
        }

        if ($user_info['is_complete'] == 1) { // 已填写完整信息，有 3 次投票次数
            $result = (new UserModel())->save([
                'frequency' => 3,
            ], ['id' => $user_info['id']]);
        }

        $user_info = UserModel::get($user_info['id']);
        return $user_info;
    }

    // 查看当天是否有投票
    private function checkUserDateHasFrequency($cache)
    {
        $date = date('Y-m-d');
        $record = (new VotingRecordModel())->where('user_id', $cache['id'])
            ->where('voted', $date)
            ->select();

        if (empty($record)) { // 如果用户当天没投票
            return false;
        }

        return true;
    }
}