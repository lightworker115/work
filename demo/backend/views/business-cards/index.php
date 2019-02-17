<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Json;
// 定义标题和面包屑信息
$this->title = '员工名片';
?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        aStatusColor = <?=Json::encode($statusColor)?>;
    var enterprise = <?=Json::encode(Yii::$app->session->get('enterprise'))?>;
    var m = meTables({
        title: "员工名片",
        
        table: {
            "aoColumns": [
              {"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden", "required": true,"number": true},"bSortable": false,"bViews":false},
			{"title": "企业id", "data": "enterprise_id","isHide":true, "edit": {"type": "hidden","readonly":true, "required": true,"number": true,"value":enterprise}, "bSortable": false,"bViews":false},
//			{"title": "人气", "data": "popularity", "bSortable": false},
//			{"title": "靠谱", "data": "reliable", "bSortable": false},
//			{"title": "转发", "data": "share", "bSortable": false},
//			{"title": "查看网站数量", "data": "see_website", "bSortable": false},
//			{"title": "查看商品数量", "data": "see_pro", "bSortable": false},
//			{"title": "查看朋友圈数量", "data": "see_dynamic", "bSortable": false},
//			{"title": "保存电话数量", "data": "copy_phone", "bSortable": false},
//			{"title": "保存邮箱数量", "data": "copy_email", "bSortable": false},
//			{"title": "复制微信", "data": "copy_wechat", "bSortable": false},

//                {"title": "头像", "data": "portrait",  "isHide": true,"edit": {
//                    "type": "file",
//                    options: {
//                        "id": "file",
//                        "name": "UploadForm[portrait]",
//                        "input-name": "portrait",
//                        "input-type": "ace_file",
//                        "file-name": "portrait"
//                    }
//                }
//                },
			{"title": "名称", "data": "name", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "bSortable": false}, 
			{"title": "电话", "data": "tel", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "search": {"type": "text"}, "bSortable": false}, 
			{"title": "微信", "data": "wechat", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "bSortable": false},
			{"title": "职位", "data": "position", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "bSortable": false}, 
			{"title": "公司", "data": "company", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "bSortable": false}, 
			{"title": "邮件", "data": "email", "edit": {"type": "text", "required": true,"rangelength": "[2, 50]"}, "bSortable": false}, 
			{"title": "详情", "data": "details", "edit": {"type": "text", "required": true}, "bSortable": false},
                {"title": "创建时间", "data": "created_at",  "sName": "created_at", "createdCell" : meTables.dateTimeString},
                {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : meTables.dateTimeString},
                {
                    "title": "状态",
                    "data": "status",
                    "value": aStatus,
                    "edit": {"type": "radio", "default": 10, "required": true, "number": true},
                    "bSortable": false,
                    "search":{"type":"select"},
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aStatus, aStatusColor, data));
                    },
                    "editable":{"type":"select"}
                }
            ]       
        },
        operations: {
            width: "200px", // 这列的宽度
            buttons: {
                "records":{
                    "title":"查看二维码",
                    "button-title": "查看二维码", // 按钮显示文字
                    "className": "btn-primary",// 按钮class 样式 标签
                    "cClass":"qrcode",// 按钮和a 标签共用class 标签
                    "icon":"fa-list-alt", // a 标签 icon 图标
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
         $(document).on("click", ".qrcode", function(){
             var index = $(this).attr("table-data"),// 这个是获取到表格的第几行
                 data = m.table.data()[index];// 获取到这一行的数据
             if (data) {
//                 console.log(data);
                 // 拿到数据了，做相应的处理
                 /**
                  *获取对应的地址ajax去访问
                  */
                 $.ajax({
                     url:"<?=\yii\helpers\Url::toRoute('get-qrcode')?>",
                     data:{'card_id':data.id},
                     success:function (e) {
                            $("#qrcode").html('<img style="height: 427px" src='+ e+'>')
                     }
                 });
                 layer.open({
                     type: 1,
                     title: false ,//不显示标题栏
                     closeBtn: false,
                     area: '435px;',
                     shade: 0.8,
                     id: 'LAY_layuipro', //设定一个id，防止重复弹出
                     btn: [ '关闭'],
                     btnAlign: 'c',
                     moveType:0, //拖拽模式，0或者1
                     content: '<div id="qrcode" style="width: 400px;height: 427px;"></div>',
                     success: function(layero){

                     }
                 });
             }
         });
     });
</script>
<?php $this->endBlock(); ?>