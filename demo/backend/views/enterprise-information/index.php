<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\json;
use yii\helpers\Url;
// 定义标题和面包屑信息
$this->title = '企业资讯';
?>

    <style>
        .add{
            margin-left: 230px;
            margin-bottom: -52px;
            text-align: center;
        }
    </style>
    <button class="btn btn-white btn-primary btn-bold add" id="add"><i class="ace-icon fa fa-plus-circle blue"></i>添加  </button>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        aStatusColor = <?=Json::encode($statusColor)?>;
    var m = meTables({
        title: "企业资讯",
        fileSelector: ["#file"],
        table: {
            "aoColumns": [
             {"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
			{"title": "企业id", "data": "enterprise_id","isHide":true, "edit": {"type": "text", "required": true,"number": true}, "bSortable": false,"bViews":false},
//			{"title": "状态", "data": "status", "edit": {"type": "text", "required": true,"number": true}, "bSortable": false},
                {"title": "图片", "data": "img",  "isHide": false,
                    "createdCell":function (td,data) {
                        $(td).html(mt.imgString1(data))
                    }
                },
			{"title": "标题", "data": "title", "edit": {"type": "text", "required": true,"rangelength": "[2, 255]"}, "search": {"type": "text"}, "bSortable": false},


                {"title": "详情", "data": "details", "isHide":true, "edit": {"type": "textarea", "rows": 6, "required": true, "rangelength": "[2, 255]"}, "bSortable": false},
                {"title": "创建时间", "data": "created_at",  "sName": "created_at", "createdCell" : meTables.dateTimeString},
                {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : meTables.dateTimeString},
                {
                    "title": "状态",
                    "data": "status",
                    "value": aStatus,
                    "edit": {"type": "radio", "default": 10, "required": true, "number": true},
                    "bSortable": false,
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aStatus, aStatusColor, data));
                    }
                },
                {"title": "排序", "data": "sort", "edit": {"type": "text", "required": true,"number": true}, "bSortable": false}
            ]       
        },
        operations:{
            width: "200px", // 这列的宽度
            buttons: {
                update:{bShow:false},
                // 其他按钮配置
                "other": {
                    "title": "修改",// a 标签 的title 属性
                    "button-title": "修改", // 按钮显示文字
                    "className": "btn-primary",// 按钮class 样式 标签
                    "cClass":"role-see",// 按钮和a 标签共用class 标签
                    "icon":"fa-pencil-square-o", // a 标签 icon 图标
                    "sClass":"yellow" // a标签自己的class 标签
                }
            }
        }
        ,
        buttons: {
            "create": {
                "bShow": false
            },
            "updateAll":{
                "bShow":false
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
     });
     $("#add").click(function () {
         $.ajax({
             url:"<?=\yii\helpers\Url::toRoute('temp')?>",

             success:function (e) {
                 console.log(e)
             }
         })
     });
    $(document).on("click", ".role-see", function(){
        var index = $(this).attr("table-data"),// 这个是获取到表格的第几行
            data = m.table.data()[index];// 获取到这一行的数据
        if (data) {
            $.ajax({
                    'url':'<?=Url::toRoute('temp')?>',
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
</script>
<?php $this->endBlock(); ?>