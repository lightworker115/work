<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Json;
// 定义标题和面包屑信息
$this->title = '菜谱';
?>
    <button class="btn btn-white btn-primary btn-bold add" style="margin-left: 232px;margin-bottom: -52px;text-align: center;"><i class="ace-icon fa fa-plus-circle blue"></i>添加</button>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        pops = <?=Json::encode($pops)?>,
        aStatusColor = <?=Json::encode($statusColor)?>;
    var m = meTables({
        title: "菜谱",
        
        table: {
            "aoColumns": [
                {"title": "id", "data": "id", "edit": {"type": "hidden", }, "bSortable": false},
                {"title": "类别id ", "data": "cid", "edit": {"type": "text", "number": true}, "bSortable": false},
			{"title": "标题", "data": "title", "edit": {"type": "text", "rangelength": "[2, 50]"}, "search": {"type": "text"}, "bSortable": false}, 
			{"title": "图片", "data": "logo", "edit": {"type": "text", "rangelength": "[2, 255]"},
                "createdCell": function (td, data) {
                    $(td).html(mt.imgStrings(data));
                }, "bSortable": false},
			{"title": "详情图", "data": "imgs", "edit": {"type": "text", "rangelength": "[2, 255]"}, "bSortable": false}, 
			{"title": "详情", "data": "detail", "edit": {"type": "text", }, "bSortable": false}, 
			{"title": "浏览人数", "data": "see_num", "bSortable": false},
                {
                    "title": "状态",
                    "data": "status",
                    "value": aStatus,
                    "edit": {"type": "select", "default": 1, "required": true, "number": true},
                    "bSortable": false,
                    "search":{"type":"select"},
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aStatus, aStatusColor, data));
                    },
                    "editable":{"type":"select"}
                },
                {
                    "title": "是否推荐",
                    "data": "is_pop",
                    "value": pops,
                    "edit": {"type": "radio", "default": 1, "required": true, "number": true},
                    "bSortable": false,
                    "search":{"type":"select"},
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(pops, aStatusColor, data));
                    },
                    "editable":{"type":"select"}
                },
                {"title": "创建时间", "data": "created_at",  "sName": "created_at", "createdCell" : meTables.dateTimeString},
                {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : meTables.dateTimeString},


            ]       
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

     $(function(){
         m.init();
     });
    //     添加商品事件
    $('.add').click(function () {
        $.ajax({
            url:"<?=\yii\helpers\Url::toRoute('add')?>",
            success:function (e) {
                console.log(e)
            }
        })
    });
</script>
<?php $this->endBlock(); ?>