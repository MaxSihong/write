<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::group('', function () {
    Route::group('api', function () {
        // user
        Route::group('user', function () {
            Route::post('login', 'api/user.login/login'); // 登录

            Route::get('mine', 'api/user.user/mine'); // 我的页面
            Route::post('certification', 'api/user.user/studentCertification'); // 学生认证
            Route::post('user_info', 'api/user.user/addUserInfo'); // 填写完整信息
            Route::put('check', 'api/user.user/check'); // 签到

            Route::get('candidate', 'api/user.user/candidate'); // 获取座位号等信息

            Route::get('vote', 'api/user.vote/index'); // 拉票
            Route::get('vote/:id', 'api/user.vote/get'); // 助力页面 传考生ID
            Route::put('vote/:id', 'api/user.vote/boost'); // 投票

            Route::get('record/:status', 'api/VotingRecord/index'); // 0 获票 和 1 投票记录
        });

        Route::get('index', 'api/Index/index'); // 首页

        Route::get('get_time', 'api/Index/getTime'); // 获取投票开始和结束时间

        Route::get('get_code', 'api/WCMiniProgram/getMPCode'); // 生成签到扫的二维码 (不需要使用)
    });
});
