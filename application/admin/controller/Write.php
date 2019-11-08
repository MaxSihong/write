<?php

namespace app\admin\controller;

use app\admin\model\Code;
use app\admin\model\Code as CodeModel;
use app\common\controller\Backend;
use think\Db;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Write extends Backend
{

    /**
     * Write模型对象
     * @var \app\admin\model\Write
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Write;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->with('bag')
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }

                    try{
                        $code = CodeModel::where('number',$params['number'])->where('is_used',0)->findOrFail();
                    }catch (ModelNotFoundException $exception){
                        $this->error(__('核销码不存在或已被核销'));
                    }

                    $codes_arr = $this->newArray($params);

                    $info = $this->infoArray($codes_arr);

                    $params['bag_id'] = $code->bag_id;
                    $params['info'] = \GuzzleHttp\json_encode($info,JSON_UNESCAPED_UNICODE);
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    private function newArray($params)
    {
        $dj =  ['11','09','06','03'];

        $pre = substr($params['number'],0,2);
        $cen = substr($params['number'],2,2);
        $end = substr($params['number'],-3,3);

        $i = 0;
        foreach ($dj as $key => $item){
            if ($item === $cen){
                $i = $key;
            }
        }
        $brr = [];
        for ($k = $i;$k<count($dj);$k++){
            $number = $pre.$dj[$k].$end;
            $code_resulte = Db::name('code')->where('number',$number)->where('is_used',1)->find();
            if ($code_resulte){
                $this->error(__('此码已被锁定，不能核销'));
            }
            array_push($brr,$pre.$dj[$k].$end);
        }

        Db::name('code')->whereIn('number',$brr)->update([
            'is_used' => 1
        ]);

        for ($w = $i-1; $w>=0;$w--){
            $num = $pre.$dj[$w].$end;
            array_unshift($brr,$num);
        }

        $codes_arr = Db::name('code')->whereIn('number',$brr)->select();

        return $codes_arr;
    }

    private function infoArray($codes_arr)
    {

        $info = [];
        foreach ($codes_arr as $item){
            $str = $item['is_used'] ? '已核销':'未核销';
            array_push($info,$item['number'].$str);
        }

        return $info;
    }

}
