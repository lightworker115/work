<?php

use jinxing\admin\widgets\MeTable;
// 定义标题和面包屑信息
$this->title = '测试';
?>
<?php echo '你好' ?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var m = meTables({
        title: "测试",
        
        table: {
            "aoColumns": [
                			{"title": "id", "data": "id", "edit": {"type": "hidden", }, "bSortable": false}, 
			{"title": "name", "data": "name", "edit": {"type": "text", "required": true,"rangelength": "[2, 23]"}, "search": {"type": "text"}, "bSortable": false}, 

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