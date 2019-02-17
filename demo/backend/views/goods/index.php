<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\json;
use yii\helpers\Url;
use jinxing\admin\AdminAsset;
// 定义标题和面包屑信息
$this->title = '商品管理';

?>
    <style>
        strong{
            font-size: 13px;
            padding-left: 10px;
        }
        span{
            padding-left: 10px;
        }
        .stock{
            color: red;
            font-weight: 800;

        }
    </style>
<button class="btn btn-white btn-primary btn-bold add" style="margin-left: 232px;margin-bottom: -52px;text-align: center;"><i class="ace-icon fa fa-plus-circle blue"></i>添加</button>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status,JSON_FORCE_OBJECT)?>,
        aStatusColor = <?=Json::encode($statusColor,JSON_FORCE_OBJECT)?>;
    var m = meTables({
        title: "商品管理",
        fileSelector: ["#file"],
        editable:true,
        table: {
            "aoColumns": [
                			{"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden"}, "bSortable": false,"bViews":false},
			{"title": "商品名", "data": "goods_name", "edit": {"type": "text", "rangelength": "[2, 33]"}, "search": {"type": "text"}, "bSortable": false},
            {"title": "商品标题", "data": "goods_title","bViews":false,"isHide":true, "edit": {"type": "text", "rangelength": "[2, 33]"}, "search": {"type": "text"}, "bSortable": false},
           {"title": "商品详情", "data": "spec_val", "edit": {"type": "hidden", "rangelength": "[2, 33]"}, "bSortable": false},
//                {"title": "商品库存", "data": "stock", "edit": {"type": "hidden", "rangelength": "[2, 33]"}, "bSortable": false},
//                {"title": "商品价格", "data": "price", "edit": {"type": "hidden", "rangelength": "[2, 33]"},  "bSortable": false},
//                {"title": "商品图", "data": "img", "edit": {"type": "text", "rangelength": "[2, 50]"}, "bSortable": false},
                {"title": "商品图片", "data": "img",  "isHide": true,"bViews":false},
//                {"title": "运费", "data": "freight", "edit": {"type": "text", "rangelength": "[2, 50]"}, "bSortable": false},
                {
                    "title": "状态",
                    "data": "status",
                    "value": aStatus,
                    "edit": {"type": "radio", "default": 0, "required": true, "number": true},
                    "search": {"type": "select"},
                    "bSortable": false,
                    "createdCell" : function (td,data) {
                        $(td).html(mt.valuesString1(aStatus,'',data))
                    }
                },
                {"title": "上架日期", "data": "start_time", "edit": {"type": "hidden", "number": true}, "createdCell" : meTables.dateTimeString},
                {"title": "下架日期", "data": "end_time", "edit": {"type": "hidden", "number": true}, "createdCell" : meTables.dateTimeString},
//			{"title": "创建时间", "data": "created_at", "edit": {"type": "hidden", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
//			{"title": "更新时间", "data": "updated_at", "edit": {"type": "hidden", "number": true}, "createdCell" : meTables.dateTimeString},

            ]
        },
        operations: {
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
        },
        buttons:{
            "create":{
                "bShow":false
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
//    var $file = null;
//    mt.fn.extend({
//        beforeShow: function (data) {
//            $file.ace_file_input("reset_input");
//            // 修改复值
//            if (this.action == "update" && !empty(data.img)) {
//                $file.ace_file_input("show_file_list", [data.img]);
//            }
//
//            return true;
//        }
//    });

     $(function(){
         m.init();

     });
//     添加商品事件
     $('.add').click(function () {
         $.ajax({
             url:"<?=\yii\helpers\Url::toRoute('add-goods')?>",
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
                    'url':'<?=Url::toRoute('edit')?>',
                    'method':'get',
                    'dataType':'application/json',
                    'data':{'goods_id':data.id},
                    success:function (e) {
                    }
                }
            )
        }
    });

</script>
<?php $this->endBlock(); ?>