<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"/Applications/MAMP/htdocs/fastadmin/public/../application/admin/view/candidate/edit.html";i:1573549743;s:78:"/Applications/MAMP/htdocs/fastadmin/application/admin/view/layout/default.html";i:1572491542;s:75:"/Applications/MAMP/htdocs/fastadmin/application/admin/view/common/meta.html";i:1572491542;s:77:"/Applications/MAMP/htdocs/fastadmin/application/admin/view/common/script.html";i:1572491542;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-name" data-rule="required" class="form-control" name="row[name]" type="text" value="<?php echo htmlentities($row['name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-phone" class="form-control" name="row[phone]" type="text" value="<?php echo htmlentities($row['phone']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Candidate_number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-candidate_number" data-rule="required" class="form-control" name="row[candidate_number]" type="text" value="<?php echo htmlentities($row['candidate_number']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Seat_number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-seat_number" data-rule="required" class="form-control" name="row[seat_number]" type="text" value="<?php echo htmlentities($row['seat_number']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Candidate_region'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-candidate_region" data-rule="required" class="form-control" name="row[candidate_region]" type="text" value="<?php echo htmlentities($row['candidate_region']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-number" data-rule="required" class="form-control" name="row[number]" type="number" value="<?php echo htmlentities($row['number']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_check'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-is_check" data-rule="required" class="form-control" placeholder=" 0是未签到，1是已签到" name="row[is_check]" type="number" value="<?php echo htmlentities($row['is_check']); ?>">
        </div>
    </div>
    <input type="hidden" name="row[user_id]" value="<?php echo htmlentities($row['user_id']); ?>">
    <!--    <div class="form-group">-->
    <!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('User_id'); ?>:</label>-->
    <!--        <div class="col-xs-12 col-sm-8">-->
    <!--            <input id="c-user_id" data-rule="required" data-source="user/user/index" data-field="nickname" class="form-control selectpage" name="row[user_id]" type="text" value="<?php echo htmlentities($row['user_id']); ?>">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="form-group">-->
    <!--        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Create_at'); ?>:</label>-->
    <!--        <div class="col-xs-12 col-sm-8">-->
    <!--            <input id="c-create_at" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[create_at]" type="text" value="<?php echo $row['create_at']?datetime($row['create_at']):''; ?>">-->
    <!--        </div>-->
    <!--    </div>-->
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>