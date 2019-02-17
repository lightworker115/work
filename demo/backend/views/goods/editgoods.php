<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 9:33
 */

$this->title = '商品编辑';
use yii\helpers\Url;
use common\widgets\ueditor\Ueditor;
use common\widgets\file_upload\FileUpload;
use jinxing\admin\AdminAsset;
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];
$this->registerCssFile($url . '/css/bootstrap-datetimepicker.css', $depends);
$this->registerCssFile($url . '/css/iconfont.css', $depends);
$this->registerCssFile($url . '/css/icon/iconfont.css', $depends);
$this->registerJsFile($url . '/js/date-time/bootstrap-datetimepicker.min.js', $depends);
$this->registerJsFile($url . '/js/date-time/moment.min.js', $depends);
?>
<style>
    *{
        margin: 0;
        padding: 0;
    }
    .img{
        width: 50px;
        height:50px;
        /*border-radius: 50%;*/
        background-color: darkseagreen;
        margin-right: 200px;
    }
    #img2,#img3,#img4{
        background-color: darkgrey;
    }
    .number{
        line-height: 50px;
        margin-left: 5px;
        font-family: "Roboto", sans-serif;
        font-size: 29px;
        color: white;
    }
    /*.second,.third,.last{*/
    /*display: none;*/
    /*}*/
    /*first样式*/
    .input-group .form-control{
        height: 57px;
        width: 585%;
    }
    .input-group-addon{
        width: 200px;
    }
    .form-group {
        margin-bottom: 12px;
        width: 1130px;
        margin-top: 20px;
        margin-left: 218px;
    }
    label {
        font-weight: 400;
        font-size: 16px;
        padding-left: 68px;
    }
    .recommend {
        margin-top: 36px;
        margin-left: 339px;
    }
    .foot {
        text-align: center;
        margin-top: 38px;
    }
    /*second*/
    #next{
        position: absolute;
        bottom: 10px;
        left: 50%;
    }
    /*third*/
    #last{
        position: absolute;
        bottom: -31px;
        left: 40%;

    }
    /*last*/
    .font {
        margin: 61px 354px;
        border: 0.6px solid silver;
        border-radius: 14%;
        width: 500px;
        height: 200px;
        box-shadow: 0 0 15px green;
        text-align: center;
        line-height: 200px;
    }
    .font span  {
        margin-left: 8px;
    }
    .backgoods{
        margin-left: 50%;
    }
    .cut{
        position:absolute;
        top:3px;
        right:-34px;
        z-index:10;
        background-color: white;
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
    .spec{
        display: inline-block;
    }
    .spec>.input-group{
        float: left;
    }
    .input-group{
        position: relative;
    }
    .spec>.input-group>.form-control{
        width: 222px;
    }
    .input-group-addon {
        width: 111px;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
         background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    #add{
        margin-left: 651px;
        /*margin-top: -174px;*/
        position: absolute;
        top:88px;
    }
    button{
        outline:none;
    }
    .layui-input-block {
        width: 1040px;
        height: 97px;
        margin-left: -69px;
        margin-top: -12px;
    }
    .layui-input-block>select{
        padding: 0 100px;
        height: 30px;
    }
    .submit{
        margin-left: 10px;
        width: 102px;
    }
    .back{
        margin-left: 400px;
        width: 102px;
    }
    .layui-input-inline{
        position: relative;

    }
    .layui-inline{
        margin-bottom: 77px;
    }
    .daterange{
        margin-left: -69px;
    }
    .layui-input-inline>.end{
        position: absolute;
        left: 207px;
        top:0;
    }

    /*.third{*/
        /*margin-top: 20px;*/
    /*}*/
</style>
<?php $this->beginBody()?>
<div class="container">
    <div class="main col-md-12 col-xs-12">

        <form class="bs-example bs-example-form" role="form" action="<?=Url::toRoute('update')?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$data->id?>">
            <!--    one-->
            <input type="hidden" class="form-control" name="enterprise_id" value="<?=$data->enterprise_id?>">
                <div class="input-group">
                    <span class="input-group-addon">商品名:</span>
                    <input type="text" class="form-control" placeholder="请输入商品名" name="goods_name" value="<?=$data->goods_name?>">
                </div>

                <div class="input-group" type="hidden" style="display: none">
                    <span class="input-group-addon">副标题:</span>
                    <input type="hidden" class="form-control" placeholder="请输入商品副标题" name="goods_title"  value="<?=$data->goods_title?>">
                </div>

                <div class="input-group">
                    <span class="input-group-addon" >图片:</span>
                    <?php echo FileUpload::widget(["config" => [ 'serverUrl' => Url::to(['uploads','action'=>'uploadimage'])],'value'=>$data->img]);?>
                </div>

            <button id="add"  type="button" style="border: 0;background: white;"><i class="iconfont icon-add" style="font-size:45px"></i><br/><strong>* 添加规格</strong></button>
<!--            <button id="delete" class="btn btn-warning" type="button">删除规格值</button>-->
            <?php foreach ($arr as $v):?>
                <div class="spec">
                    <div class="input-group val">
                        <span class="input-group-addon " >规格:</span>
                        <input type="text" class="form-control " name="spec_val[]" value= <?=$v['spec_val']?>>
                     </div>
                    <div class="input-group stock"><span class="input-group-addon" >库存:</span>
                         <input type="text" class="form-control" name="stock[]" value="<?=$v['stock']?>">
                    </div>
                    <div class="input-group price"><span class="input-group-addon" >价格:</span>
                        <input type="text" class="form-control" name="price[]" value="<?=$v['price']*0.01?>">
                        <button type="button" class="cut" data-toggle="popover" title="点击删除"><i class="iconfont icon-shanchu"></i></button>
                    </div>
                </div>
            <?php endforeach;?>
            <!--    three 商品详情-->
            <div class="third">
                <?php echo Ueditor::widget([
                    'name'=>'detail',
                    'value'=>$data->detail,
                    'options'=>[
                        'initialFrameWidth'=>1040,
                        'initialFrameHeight'=>300,
                        'autoHeightEnabled'=> false
                    ]
                ])?>
                <!--   限购数量 -->
                <div class="input-group" style="margin-top: 28px">
                    <span class="input-group-addon" style="border:0;position: relative;font-size: 16px;left: -31px;" >* 限购数量:</span>
                    <input type="text" class="form-control" placeholder="" name="limit_num" style="width:250px;height: 32px;margin-left: -33px" value="<?=$data->limit_num?>">
                    <span style="color: red;display: block;float: left;margin-top: 10px;">*不限购不用填写</span>
                </div>
                <!--状态选择-->
<!--                <div class="layui-form-item">-->
                    <div class="layui-input-block">
                        <label class="layui-form-label" style="margin-top: 36px">状态:    </label>
                        <select name="status" lay-verify="required" style="    margin-left: 38px;">
                            <option value="0" <?php if ($data->status == 0):?>selected<?php endif;?>>下架</option>
                            <option value="1" <?php if ($data->status == 1):?>selected<?php endif;?>>上架</option>
                        </select>
                    </div>
<!--                时间区间-->
                <div class="layui-inline">
                    <label class="layui-form-label daterange" style="position: relative;top: 33px;">时间范围:</label>
                    <div class="layui-input-inline" style="width: 100px;margin-left: 80px;">
                        <input type="text" name="start_time"  placeholder="开始时间" autocomplete="off" class="layui-input start" value="<?=$data->start_time?>">
                        <input type="text" name="end_time" placeholder="结束时间" autocomplete="off" class="layui-input end" value="<?=$data->end_time?>">
                    </div>
                </div>
<!--                </div>-->
            </div>
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <!--    four添加完成-->
            <div class="last">
                <div class="row">
                    <button class="btn btn-warning back" type="button">后退</button>
                    <button class="btn btn-primary submit" type="submit">提交</button>
                </div>
            </div>

        </form>
    </div>

</div>
<?php $this->endBody()?>
<?php $this->beginBlock('javascript')?>
    <script type="text/javascript">
        var valHtml = '<div class="input-group val">'+
                '<span class="input-group-addon " >规格值:</span>'+
                '<input type="text" class="form-control" name="spec_val[]" >'+
            '</div>'+
            '<div class="input-group stock"><span class="input-group-addon" >库存:</span>'+
                '<input type="text" class="form-control" name="stock[]" >'+
            '</div>'+
            '<div class="input-group price"><span class="input-group-addon" >价格:</span>'+
            '<input type="text" class="form-control" name="price[]">'+
                '<button type="button" class="cut" data-toggle="popover" title="点击删除"><i class="iconfont icon-shanchu"></i></button>'+
            '</div>';
        $(document).ready(function() {
            $('#add').click(function () {
                if ($('.price').length >0){
                    $('.price:last').after(valHtml)
                }else{
                    $('#add').after(valHtml);
                }
            });
            $(document).on('click','.cut',function () {
                $(this).parent().prev().prev().remove();
                $(this).parent().prev().remove();
                $(this).parent().remove();

            });
//            时间选择器事件

            $('.start').datetimepicker({
                format:"YYYY-MM-DD H:mm:ss"

            }).bind('changeDate',function (e) {
                var time = $('.start').val();
                $('.end').datetimepicker({
                    format:"YYYY-MM-DD H:mm:ss",
                    minDate:time
                })
            });
            $('.end').datetimepicker({
                format:"YYYY-MM-DD H:mm:ss"
            });

        });
        $('.back').click(function () {
            window.history.back();
        })
    </script>
<?php $this->endBlock();?>
