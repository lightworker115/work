<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/25
 * Time: 13:47
 */
$this->title = '修改招聘信息';
use yii\helpers\Url;
use common\widgets\ueditor\Ueditor;
use common\widgets\file_upload\FileUpload;
?>
<style>
    *{
        margin: 0;
        padding: 0;
    }
    input{
        border: 1px solid #E8E8E8 !important;
    }
    .img{
        width: 50px;
        height:50px;
        border-radius: 50%;
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
        height: 40px;
        width: 245px;
        margin-bottom: 10px;
    }
    .input-group-addon{
        border: 0 !important;
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
        margin-top: 20px;
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
        top:155px;
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
        margin-left: 503px;
        width: 102px;
    }
    .layui-input-inline{
        position: absolute;
        top: 0;
        left: 105px;
    }
    .layui-inline{
        margin-bottom: 77px;
        margin-top: -26px;
        position: relative;
    }
    .daterange{
        margin-left: -63px;
    }
    .layui-input-inline>.end{
        position: absolute;
        left: 207px;
        top:0;
    }

</style>

<div class="container">
    <div class="main col-md-12 col-xs-12">
        <form class="bs-example bs-example-form" role="form" action="<?=Url::toRoute('addinfo')?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" >
            <div class="input-group">
                <!--            <span class="input-group-addon">* 企业id:</span>-->
                <input type="hidden" class="form-control" placeholder="企业id" name="enterprise_id" value="<?=Yii::$app->session->get('enterprise')?>">
            </div>

            <div class="input-group">
                <span class="input-group-addon">* 职位:</span>
                <input type="text" class="form-control" placeholder="请输入职位" name="position" >
            </div>

            <div class="input-group">
                <span class="input-group-addon" >* 招聘人数:</span>
                <input type="number" class="form-control" placeholder="请输入招聘人数" name="num" min="0" >
            </div>

            <div class="input-group">
                <span class="input-group-addon">* 专业:</span>
                <input type="text" class="form-control" placeholder="请输入专业" name="major" >
            </div>

            <div class="input-group">
                <span class="input-group-addon">* 学历:</span>
                <input type="text" class="form-control" placeholder="请输入学历" name="education">
            </div>

            <div class="input-group">
                <span class="input-group-addon">* HR电话:</span>
                <input type="text" class="form-control" placeholder="请输入电话" name="phone">
            </div>

            <div class="input-group" style="margin-top: 10px">
                <span class="input-group-addon">* 排序:</span>
                <input type="text" class="form-control" placeholder="" name="sort">
            </div>

            <div class="input-group">
                <div style="width: 800px;height:548px; ">
                    <span style="position: relative;top: 228px;left: 18px">*描述：</span>
                    <div style="margin-left: 110px;margin-top: 27px">
                        <?php echo Ueditor::widget([
                            'name'=>'details',
                            'options'=>[
                                'initialFrameWidth'=>800,
                                'initialFrameHeight'=>300,
                            ]
                        ])?>
                    </div>
                </div>
            </div>

            <!--状态选择-->
            <div class="layui-form-item" style="margin-top: -38px;margin-left: 61px;position: relative">
                <div class="layui-input-block">
                    <label class="layui-form-label" style="margin-top: 36px;position: absolute;top: -31px;left: -96px;">* 状态:    </label>
                    <select name="status" lay-verify="required" style="position: absolute;top: 0;left: 46px;">
                        <option value="1" selected>启用</option>
                        <option value="2" >未启用</option>
                    </select>
                </div>
            </div>

            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken?>">
            <div   style=" position: relative;top: -9px;left: 438px;">
                <button type="reset" class="btn btn-default" >取消</button>
                <button type="submit" class="btn btn-primary" >确定</button>
            </div>
        </form>
        <div>
        </div>
