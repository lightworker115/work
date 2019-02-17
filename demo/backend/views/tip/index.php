<?php

use jinxing\admin\widgets\MeTable;
// 定义标题和面包屑信息
$this->title = '公告';
?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var m = meTables({
        title: "公告",
        
        table: {
            "aoColumns": [
                			{"title": "id", "data": "id", "edit": {"type": "hidden", }, "bSortable": false}, 
			{"title": "标题", "data": "title", "edit": {"type": "text", "rangelength": "[2, 30]"}, "bSortable": false},
			{"title": "内容", "data": "detail", "edit": {"type": "textarea", "rangelength": "[2, 255]"}, "bSortable": false},
                {"title": "创建时间", "data": "created_at",  "sName": "created_at", "createdCell" : meTables.dateTimeString},
                {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : meTables.dateTimeString},
            ]       
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
</script>
<?php $this->endBlock(); ?>