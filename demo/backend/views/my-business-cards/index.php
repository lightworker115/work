<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Url;
use common\widgets\file_upload\FileUpload;
use common\widgets\ueditor\Ueditor;
// 定义标题和面包屑信息
$this->title = '名片';
?>
    <style>
        .input-group .form-control{
            height: 50px;
            border: 1px solid #E8E8E8;
            width: 123%;
        }
        .input-group{
            width: 760px;
            margin-top: 10px;
        }
        .input-group-addon{
            border: 0;
            background: #ffffff;
            width: 160px;
        }
        .bottoms{
            margin-left: 493px;
            margin-top: 187px;
            /*position: fixed;*/
            /*bottom: 150px;*/
        }
        #portrait{
            width: 48px;
            height: 48px;
            border: 0.6px solid red;
            margin-left: 20px;
            margin-top: 0px;
            border-radius: 50%;
        }
        .headimg{
            margin-left: 40px;
            margin-top:-10px;
            border-right: 0;
        }
        .name{
            height: 50px;
        }
        .detail{
            position: relative;
            display: inline-block;
            width: 160px;
            height: 118px;
            line-height: 118px;
        }
        #detail{
            position: absolute;
            left: 160px;
            top: 0;

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
        .img{
            border-radius: 0;
        }
    </style>
<div class="container">
    <form action="<?=Url::toRoute('add')?>" method="post">
        <div class="input-group">
            <input type="hidden" name="id" value="<?=$data->id?>">
            <div class="input-group">
                <span class="input-group-addon" >* 图片:</span>
                <?php echo FileUpload::widget(["config" => [ 'serverUrl' => Url::to(['uploads','action'=>'uploadimage'])],'value'=>$data->portrait]); ?>
            </div>
        </div>
        <div class="input-group">
            <span class="input-group-addon">名称:</span>
            <input type="text" class="form-control" name="name" value="<?=$data->name?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">电话:</span>
            <input type="text" class="form-control" name="tel" value="<?=$data->tel?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">微信:</span>
            <input type="text" class="form-control" name="wechat" value="<?=$data->wechat?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">职位:</span>
            <input type="text" class="form-control" name="position" value="<?=$data->position?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">公司:</span>
            <input type="text" class="form-control" name="company" value="<?=$data->company?>">
        </div>
        <div class="input-group">
            <span class="input-group-addon">邮箱:</span>
            <input type="text" class="form-control" name="email" value="<?=$data->email?>">
        </div>
        <div style="width: 800px;height: 310px; ">
            <span class="input-group-addon detail">内容:</span>
            <div style="margin-left: 158px;margin-top: -87px">
                <?php echo Ueditor::widget([
                    'name'=>'details',
                    'value'=>$data->details,
                    'options'=>[
                        'initialFrameWidth'=>800,
                        'initialFrameHeight'=>300,
                        'autoHeightEnabled'=> false
                    ]
                ])?>
            </div>
        </div>
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row bottoms">
            <button class="btn btn-primary" type="submit" >确认</button>
            <button class="btn btn-warning" type="reset">重置</button>
        </div>
<!--        --><?php //endforeach;?>
    </form>
</div>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var sUploadUrl = "<?=Url::toRoute(['upload', 'sField' => 'portrait'])?>";
    $('.portrait').on('click', function () {
        var modal =
            '<div class="modal fade">\
              <div class="modal-dialog">\
               <div class="modal-content">\
                <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal">&times;</button>\
                    <h4 class="blue">更换头像</h4>\
                </div>\
                \
                <form class="no-margin m-image" action="'+sUploadUrl+'" method="post"  enctype= "multipart/form-data">\
                     <div class="modal-body">\
                        <div class="space-4"></div>\
                        <div style="width:75%;margin-left:12%;"><input type="file" name="UploadForm[portrait]" /></div>\
                     </div>\
                    \
                     <div class="modal-footer center">\
                        <button type="submit" class="btn btn-sm btn-success"><i class="ace-icon fa fa-check"></i> 确定 </button>\
                        <button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> 取消 </button>\
                     </div>\
                    </form>\
                  </div>\
                 </div>\
                </div>';
        var modal = $(modal);
        // 取消
        modal.modal("show").on("hidden", function () {
            modal.remove();
        });

        var working = false,
            form = modal.find('form:eq(0)');
        file = form.find('input[type=file]').eq(0);

        // 图片上传
        file.ace_file_input({
            style: 'well',
            btn_choose: '点击选择新的头像',
            btn_change: null,
            no_icon: 'ace-icon fa fa-picture-o',
            thumbnail: 'small',
            before_remove: function () {
                return !working;
            },

            // 允许上传的头像
            allowExt: ['jpg', 'jpeg', 'png', 'gif'],
            allowMime: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']
        });
    })

</script>
<?php $this->endBlock(); ?>