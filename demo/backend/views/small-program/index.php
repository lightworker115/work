<?php

use yii\helpers\Url;
use common\widgets\file_upload\FileUpload;
// 定义标题和面包屑信息
$this->title = '系统配置';
?>
<style>
    .input-group .form-control{
        height: 50px;
        border:1px solid #E8E8E8;
    }
    .input-group{
        margin-top: 10px;
    }
    .bottoms {
        margin-left: 493px;
        margin-top: 37px;
    }
    .input-group-addon{
        width: 200px;
        border: 0 !important;
        background-color: white !important;
    }
    .input-group .form-control{
        width: 463%;
    }
    .nav-tabs {
        border-bottom: 0;
    }
    .tab-content{
        border: 0;
    }
    .per_real_img{
        position: absolute;
        top: -18px;
        left: -45px;
        width: 140px;
        height: 140px;
        background-color: transparent;
    }
    .per_upload_img{
        height: 148px;
        width: 148px;
    }
    img{
        width: 140px;
        height: 140px;
    }
    legend{
        font-size: 16px;
    }
</style>
<div class="header">
            <div>
                <ul id="myTab" class="nav nav-tabs">
                    <li class="home active">
                        <a href="#home" data-toggle="tab" class="home">
                            小程序配置
                        </a>
                    </li>
                    <li class="info"><a class="info" href="#info" data-toggle="tab">支付配置</a></li>
                    <li class="enterprise"><a class="info" href="#enterprise" data-toggle="tab">企业配置</a></li>
                </ul>
            </div>
</div>
<div class="container">
    <form action="<?=Url::toRoute('add')?>" method="post">
        <div class="tab-content">
            <div class="tab-body" id="home">
                <div class="input-group">
                    <input type="hidden" name="id" value="<?=$data->id?>">
                </div>
                <!--                网址-->
                <div class="input-group">
                    <span class="input-group-addon">链接:</span>
                    <input type="text" class="form-control url" name="" value="<?=$url?>" disabled style="width: 734px">
                    <button id="copy" style="" type="button">复制网址</button>
                </div>

        <div class="input-group">
            <span class="input-group-addon">appid:</span>
            <input type="text" class="form-control" name="app_id" value="<?=$data->app_id?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">appsecret:</span>
            <input type="text" class="form-control" name="app_secret" value="<?=$data->app_secret?>">
        </div>

            </div>
            <div id="info">
                <div class="input-group">
                    <span class="input-group-addon">商户号:</span>
                    <input type="text"class="form-control" name="merchant_id" value="<?=$data->merchant_id?>">
                </div>
                <div class="input-group">
                    <span class="input-group-addon">商户签名:</span>
                    <input type="text"class="form-control" name="merchant_signature" value="<?=$data->merchant_signature?>">
                </div>
            </div>
        </div>

        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken?>">
        <div class="row bottoms system">
            <button class="btn btn-primary" type="submit" >确认</button>
            <button class="btn btn-warning" type="reset">重置</button>
            <button class="btn btn-info" type="button" id="download">打包</button>
        </div>
    </form>
    <!--        企业配置-->
    <div id="enterprise">
        <form action="<?=Url::toRoute('config')?>" method="post">
            <?php if ($enterprise!=''):?>
                <input type="hidden" class="form-control" name="id" value="<?php if ($enterprise->id):?><?=$enterprise->id?><?php endif;?>">
                <input type="hidden" class="form-control" name="enterprise_id" value="<?=Yii::$app->session->get('enterprise')?>" >
                <fieldset >
                    <legend style="">企业配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">corp_id:</span>
                        <input type="text" class="form-control" name="corp_id" value="<?php if ($enterprise->corp_id):?><?=$enterprise->corp_id?><?php endif;?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">secret:</span>
                        <input type="text" class="form-control" name="secret" value="<?php if ($enterprise->secret):?><?=$enterprise->secret?><?php endif;?>" >
                    </div>
                </fieldset>
                <!--            app-->

                <fieldset>
                    <legend>应用配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">应用ID:</span>
                        <input type="text" class="form-control" name="agent_id" value="<?php if ($enterprise->agent_id):?><?=$enterprise->agent_id?><?php endif;?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">app_secret:</span>
                        <input type="text" class="form-control" name="app_secret" value="<?php if ($enterprise->app_secret):?><?=$enterprise->app_secret?><?php endif;?>" >
                    </div>
                </fieldset>

                <fieldset><legend>产品配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">program_secret:</span>
                        <input type="text" class="form-control" name="pro_secret" value="<?php if ($enterprise->pro_secret):?><?=$enterprise->pro_secret?><?php endif;?>">
                    </div>
                </fieldset>
            <?php else:?>
                <input type="hidden" class="form-control" name="id">
                <input type="hidden" class="form-control" name="enterprise_id" value="<?=Yii::$app->session->get('enterprise')?>" >
                <fieldset >
                    <legend style="">企业配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">corp_id:</span>
                        <input type="text" class="form-control" name="corp_id">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">secret:</span>
                        <input type="text" class="form-control" name="secret">
                    </div>
                </fieldset>
                <!--            app-->

                <fieldset>
                    <legend>应用配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">应用ID:</span>
                        <input type="text" class="form-control" name="agent_id">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">app_secret:</span>
                        <input type="text" class="form-control" name="app_secret" >
                    </div>
                </fieldset>

                <fieldset><legend>产品配置</legend>
                    <div class="input-group">
                        <span class="input-group-addon">program_secret:</span>
                        <input type="text" class="form-control" name="pro_secret" >
                    </div>
                </fieldset>
            <?php endif;?>
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <div class="row bottoms">
                <button class="btn btn-primary" type="submit" >确认</button>
                <button class="btn btn-warning" type="reset">重置</button>
            </div>
        </form>
    </div>



</div>
<?php $this->beginBlock('javascript')?>
<script type="text/javascript" src="http://img3.job1001.com/js/ZeroClipboard/jquery.zclip.min.js"></script>
<script type="text/javascript">
//标签页显示事件
    $('#info').hide();
    $('#enterprise').hide();
    $('.home').click(function () {
        $('#info').hide();
        $('#enterprise').hide();
        $('#home').show();
        $('.system').show();
    });
    $('.info').click(function () {
        $('#home').hide();
        $('#enterprise').hide();
        $('#info').show();
        $('.system').show();
    });
    $('.enterprise').click(function () {
        $('#home').hide();
        $('#enterprise').show();
        $('#info').hide();
        $('.system').hide();
    });
    $('#copy').zclip({
//        var e=document.getElementsByClassName("url").val();//对象是content
        path:'http://img3.job1001.com/js/ZeroClipboard/ZeroClipboard.swf',
            copy:function(){return $(this).prev('input').val();},
        afterCopy:function(){alert('复制成功！');}

    })
//    下载事件
    $('#download').click(function () {
                 window.open('http://'+document.domain+'/small-program/download');
         })

</script>
<?php $this->endBlock()?>
