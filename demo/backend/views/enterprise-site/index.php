<?php

use yii\helpers\Url;
use common\widgets\ueditor\Ueditor;
use common\widgets\file_upload\FileUpload;
// 定义标题和面包屑信息
$this->title = '企业信息编辑';
?>
<style>
    .input-group .form-control{
        height: 50px;
       border: 1px solid #E8E8E8;
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
        background-color: white !important;
        border: 0 !important;
    }
    .input-group .form-control{
        width: 463%;
    }
    .per_real_img{
        position: absolute;
        top: -18px;
        left: -45px;
        width: 140px;
        height: 140px;
        background-color: transparent;
    }
    .img{
        border-radius: 0;
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

<div class="container">
    <form action="<?=Url::toRoute('add')?>" method="post">
        <div class="input-group">
            <input type="hidden" name="id" value="<?=$data->id?>">
        </div>
        <div class="input-group">
<!--            <span class="input-group-addon">企业ID:</span>-->
            <input type="hidden" class="form-control" name="enterprise_id" value="<?=$data->enterprise_id?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon"  >* 图片:</span>
            <?php echo FileUpload::widget(["config" => [ 'serverUrl' => Url::to(['uploads','action'=>'uploadimage'])],'value'=>$data->img]); ?>
        </div>
        <div class="input-group">
            <span class="input-group-addon">地址:</span>
            <input type="text" class="form-control" name="address" value="<?=$data->address?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">联系电话:</span>
            <input type="text" class="form-control" name="tel" value="<?=$data->tel?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">客户服务热线:</span>
            <input type="text" class="form-control" name="service_hotline" value="<?=$data->service_hotline?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">传真:</span>
            <input type="text" class="form-control" name="fax" value="<?=$data->fax?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">邮箱:</span>
            <input type="text" class="form-control" name="email" value="<?=$data->email?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">网址:</span>
            <input type="text"class="form-control" name="website" value="<?=$data->website?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">短视频:</span>
            <input type="text"class="form-control" name="video" value="<?=$data->video?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">企业介绍:</span>
            <div style="margin-left: 0;margin-top: 27px">
                <?php echo Ueditor::widget([
                    'name'=>'introduction',
                    'value'=>$data->introduction,
                    'options'=>[
                        'initialFrameWidth'=>739,
                        'initialFrameHeight'=>300,
                        'autoHeightEnabled'=> false
                    ],
                ])?>
            </div>
        </div>
        <input name="_csrf-backend" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken?>">
        <div class="row bottoms">
            <button class="btn btn-primary" type="submit" >确认</button>
            <button class="btn btn-warning" type="reset">重置</button>
        </div>

    </form>
</div>
