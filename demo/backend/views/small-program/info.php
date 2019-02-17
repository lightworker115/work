<?php

use yii\helpers\Url;
use common\widgets\file_upload\FileUpload;
// 定义标题和面包屑信息
$this->title = '个人信息配置';
?>
<style>
    .input-group .form-control{
        height: 50px;
        border-left: 0;
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
    }
    .input-group .form-control{
        width: 500%;
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

</style>
<div class="header">
    <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div>
                <ul class="nav navbar-nav">
                    <li ><a href="<?=Url::toRoute('index')?>" >商家配置</a></li>
                    <li><a href="<?=Url::toRoute(['info','id'=>$data->id])?>">个人信息配置</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <form action="<?=Url::toRoute('add')?>" method="post">
        <div class="input-group">
            <input type="hidden" name="id" value="<?=$data->id?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon"  >* 图片:</span>
            <?php echo FileUpload::widget(["config" => [ 'serverUrl' => Url::to(['uploads','action'=>'uploadimage'])],"value"=>$data->head_img]); ?>
        </div>
        <div class="input-group">
            <span class="input-group-addon">昵称:</span>
            <input type="text"class="form-control" name="nick_name" value="<?=$data->nick_name?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">用户名:</span>
            <input type="text"class="form-control" name="user_name" value="<?=$data->user_name?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">alias:</span>
            <input type="text"class="form-control" name="alias" value="<?=$data->alias?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">apiclient_cert:</span>
            <input type="text"class="form-control" name="apiclient_cert" value="<?=$data->apiclient_cert?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">apiclient_key:</span>
            <input type="text"class="form-control" name="apiclient_key" value="<?=$data->apiclient_key?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">web_check:</span>
            <input type="text"class="form-control" name="web_check" value="<?=$data->web_check?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">template_group_success:</span>
            <input type="text"class="form-control" name="template_group_success" value="<?=$data->template_group_success?>">
        </div>

        <div class="row bottoms">
            <button class="btn btn-primary" type="submit" >确认</button>
            <button class="btn btn-warning" type="reset">重置</button>
        </div>

    </form>
</div>

