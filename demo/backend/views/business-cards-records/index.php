<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\json;
use jinxing\admin\AdminAsset;
use yii\helpers\Url;
// 定义标题和面包屑信息
$this->title = '客户管理';
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];

$this->registerCssFile($url . '/css/jquery-ui.custom.min.css', $depends);
$this->registerCssFile($url . '/css/bootstrap-editable.css', $depends);
$this->registerJsFile($url . '/js/jquery-ui.custom.min.js', $depends);
$this->registerJsFile($url . '/js/jquery.ui.touch-punch.min.js', $depends);
$this->registerJsFile($url . '/js/x-editable/bootstrap-editable.min.js', $depends);
$this->registerJsFile($url . '/js/x-editable/ace-editable.min.js', $depends);

?>

    <style>
        .img{
            width:66px;
            height: 48px;
        }
    </style>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status,JSON_FORCE_OBJECT)?>,
        aStatusColor = <?=Json::encode($statusColor,JSON_FORCE_OBJECT)?>,
        card = <?=Json::encode($card)?>,
        nickname = <?=Json::encode($nickname)?>,
        headimgurl = <?=Json::encode($headimgurl)?>;
    var enterprise = <?=Json::encode(Yii::$app->session->get('enterprise'))?>;
    var m = meTables({
        title: "客户管理",
        editable:true,
        table: {
            "aoColumns": [
             {"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
			{"title": "企业id", "data": "enterprise_id","isHide":true,"edit": {"type": "hidden", "required": true,"number": true,"value":enterprise,"readonly":true}, "bSortable": false,"bViews":false},
                {"title": "openid", "data": "openid","isHide":true,"bSortable": false,"bViews":false},
                {"title": "用户头像", "data": "openid","value":headimgurl, "bSortable": false, "createdCell": function (td, data) {
                    $(td).html(mt.imgString(headimgurl, data));
                },"bViews":false},
                {"title": "用户昵称", "data": "openid","value":nickname, "bSortable": false, "createdCell": function (td, data) {
                $(td).html(mt.valuesString1(nickname,'', data));
            }},

			{"title": "名称", "data": "name", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"},  "bSortable": false},
			{
			    "title": "名片",
                "data": "card_id",
                "value": card,
                "isHide":false,
                "edit": {"type": "select", "required": true,"number": true},
                "bSortable": false,
                "createdCell": function (td, data) {
                    $(td).html(mt.valuesString1(card, '', data));
                }
            },
			{"title": "转发渠道", "data": "channel", "edit": {"type": "text", "required": true,"number": true}, "bSortable": false},
                {
                    "title": "状态",
                    "data": "follow_status",
                    "value": aStatus,
                    "edit": {"type": "radio", "default": 0, "required": true, "number": true},
//                    "search": {"type": "select"},
                    "bSortable": false,
                    "editable": {
                        "type": "select"
                    },
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aStatus, aStatusColor, data));
                    }
                },
			{"title": "创建时间", "data": "created_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
			{"title": "更新时间", "data": "updated_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},

            ]
        },
        operations: {
            width: "200px", // 这列的宽度
            buttons: {
                "records":{
                    "title":"查看记录",
                    "button-title": "查看记录", // 按钮显示文字
                    "className": "btn-primary",// 按钮class 样式 标签
                    "cClass":"records",// 按钮和a 标签共用class 标签
                    "icon":"fa-list-alt", // a 标签 icon 图标
                    "sClass":"yellow" // a标签自己的class 标签
                },
                // 其他按钮配置
                "other": {
                    "title": "查看任务",// a 标签 的title 属性
                    "button-title": "查看任务", // 按钮显示文字
                "className": "btn-primary",// 按钮class 样式 标签
                "cClass":"role-see",// 按钮和a 标签共用class 标签
                "icon":"fa-pencil-square-o", // a 标签 icon 图标
                "sClass":"yellow" // a标签自己的class 标签
            }

            }
        }

    });
    
    /**
    meTables.fn.extend({
        // 显示的前置和后置操作
        beforeShow: function(data, child) {
            return true;
        },
        afterShow: function(data, child) {
            return true;
        },
        
        // 编辑的前置和后置操作
        beforeSave: function(data, child) {
            return true;
        },
        afterSave: function(data, child) {
            return true;
        }
    });
    */

     $(function(){
         m.init();
//         跳转操作
         $(document).on("click", ".role-see", function(){
             var index = $(this).attr("table-data"),// 这个是获取到表格的第几行
                 data = m.table.data()[index];// 获取到这一行的数据
             if (data) {
                 $.ajax({
                         'url':'<?=Url::toRoute("task")?>',
                         'method':'get',
                         'dataType':'application/json',
                         'data':{'id':data.id},
                         success:function (e) {

                         }
                     }
                 )
                 // 拿到数据了，做相应的处理
             }
         });

    //         跳转记录页操作
    $(document).on("click", ".records", function(){
        var index = $(this).attr("table-data"),// 这个是获取到表格的第几行
            data = m.table.data()[index];// 获取到这一行的数据
        if (data) {
            $.ajax({
                    'url':'<?=Url::toRoute("records")?>',
                    'method':'get',
                    'dataType':'json',
                    'data':{'id':data.id,'openid':data.openid},
                    success:function (e) {
                    }
                }
            )
            // 拿到数据了，做相应的处理
        }
    });
         $('.editable-click').click(function () {
         })
    });
</script>
<?php $this->endBlock(); ?>