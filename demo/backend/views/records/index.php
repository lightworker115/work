<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/28
 * Time: 10:20
 */
$this->title='客户操作记录';
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
date_default_timezone_set('PRC'); //设置中国时区
?>
<style type="text/css">
    .profile-activity span.user{
        font-size: 20px;
    }
    .profile-activity img{
        margin-right: 26px;
    }
   .op{
       padding-left: 20px;
       font-size: 16px;
       color: #478fca!important;
   }
    .pl{
        font-size:16px;
        color: #0D47A1;
    }
    .profile-activity .time{
        text-align: right;
        font-size: 14px;
    }
    .scroll-content{
        max-height: 400px;
        overflow: scroll;
    }
</style>
<?php if ($userinfo['nickname']):?>
<div>
    <div class="user-profile row" id="user-profile-1">
        <div class="col-xs-12 col-sm-3 center">
            <div>
                <span class="profile-picture">
                    <img id="avatar" class="editable img-responsive editable-click editable-empty" alt="Alex's Avatar"
                         src="<?=$userinfo->headimgurl?>"/>
                </span>
                <div class="space-4"></div>
                <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                    <div class="inline position-relative">
                        <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                            <i class="ace-icon fa fa-circle light-green"></i>
                            <span class="white"><?= $userinfo->nickname ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>
            <!-- /section:pages/profile.contact -->
            <div class="hr hr12 dotted"></div>

            <!-- #section:custom/extra.grid -->
            <div class="clearfix">

<!--            </div>-->

            <!-- /section:custom/extra.grid -->
            <div class="hr hr16 dotted"></div>
        </div>
        </div>

        <div class="col-xs-12 col-sm-9">
            <!-- #section:pages/profile.info -->
            <div class="profile-user-info profile-user-info-striped">
                <div class="profile-info-row">
                    <div class="profile-info-name"> 用户名</div>

                    <div class="profile-info-value">
                        <span id="username" class="editable editable-click"><?= $userinfo->nickname ?></span>
                    </div>
                </div>

                <div class="profile-info-row">
                    <div class="profile-info-name"> 地址</div>

                    <div class="profile-info-value">
                        <i class="fa fa-map-marker light-orange bigger-110"></i>
                        <span class="editable editable-click"><?=$userinfo->country?></span>
                        <span id="country" class="editable editable-click"><?= $userinfo->province ?></span>
                        <span id="city" class="editable editable-click"><?= $userinfo->city ?></span>
                    </div>
                </div>

                <div class="profile-info-row">
                    <div class="profile-info-name"> 性别</div>

                    <div class="profile-info-value">
                        <span id="age" class="editable editable-click">
                            <?php if ($userinfo->gender ==1): echo '男'; else: echo '女';?>
                            <?php endif;?>
                    </div>
                </div>

                <div class="profile-info-row">
                    <div class="profile-info-name"> 添加时间</div>
                    <div class="profile-info-value">
                        <span id="login_time"
                              class="editable editable-click"><?= date('Y-m-d H:i:s', $userinfo->created_at) ?></span>
                    </div>
                </div>


                <div class="profile-info-row">
                    <div class="profile-info-name"> 座右铭</div>

                    <div class="profile-info-value">
                        <span id="about" class="editable editable-click">
                            <?php if ($userinfo->subscribe): echo $userinfo->subscribe;else: echo'这个家伙太懒,什么都没有留下';?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- /section:pages/profile.info -->
            <div class="space-20"></div>

            <div class="widget-box transparent">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title blue smaller">
                        <i class="ace-icon fa fa-rss orange"></i>
                        操作记录
                    </h4>

                    <div class="widget-toolbar action-buttons">
                        <a data-action="reload" href="#">
                            <i class="ace-icon fa fa-refresh blue"></i>
                        </a>
                        <a class="pink" href="#">
                            <i class="ace-icon fa fa-trash-o"></i>
                        </a>
                    </div>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-8">
                        <div id="profile-feed-1" class="profile-feed" style="position: relative;">
                            <div class="scroll-content" >
                            <?php if ($data) : foreach ($data as $value) : ?>
                                <div class="profile-activity clearfix">
                                    <div>
                                        <img class="pull-left" alt="用户头像" src="<?= $userinfo->headimgurl ?>"/>
                                        <span class="user" href="#"> <?= date('Y年-m月-d日 H时:i分',$value['created_at'] )?> </span>
                                        <span class="op"><?= ArrayHelper::getValue($op,$value['operation_type'])?></span>
                                        了你的
                                        <span class="pl"><?= ArrayHelper::getValue($pl,$value['place'])?></span>
                                        <div class="time">
                                            <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                            <?= date('Y-m-d H:i:s', $value['created_at']) ?>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr hr2 hr-double"></div>
            <div class="space-6"></div>
        </div>
    </div>
</div>
    <?php else:?>
    <div><div>没有记录</div><button class="btn btn-default" id="back">返回上一页</button></div>
<?php endif;?>
        <?php $this->beginBlock('javascript')?>
        <script type="text/javascript">

            $('.scroll-content').scroll(function () {
                    var viewH =$(this).height();//可见高度
                    var contentH =$(this).get(0).scrollHeight;//内容高度
                    var scrollTop =$(this).scrollTop();//滚动高度
                if(contentH-scrollTop-viewH <=-10) { //到达底部时,给与提示
                    layer.msg('到底部了。。', {icon: 5});
                }
                });
            $('#back').click(function () {
                window.history.back();
            })
        </script>
        <?php $this->endBlock();?>
<!--    </div>-->
<!--</div>-->

