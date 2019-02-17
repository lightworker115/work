<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Url;
// 定义标题和面包屑信息
$this->title = '商品类型';
?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var m = meTables({
        title: "商品类型",
        table: {
            "aoColumns": [
                {"title": "类型id", "data": "id", "edit": {"type": "hidden", }, "bSortable": false},
			{"title": "类型名称", "data": "name", "edit": {"type": "text", "rangelength": "[2, 23]"}, "bSortable": false},
			{"title": "创建时间", "data": "created_at", "edit": {"type": "hidden", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
			{"title": "更新时间", "data": "updated_at", "edit": {"type": "hidden", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},

            ]
        },
        operations: {
            width: "100px", // 这列的宽度
            buttons: {
                // 其他按钮配置
                "other": {
                    "title": "查看所含属性",// a 标签 的title 属性
                    "button-title": "查看所含属性", // 按钮显示文字
                    "className": "btn-primary",// 按钮class 样式 标签
                    "cClass":"role-attr",// 按钮和a 标签共用class 标签
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
    $(document).on("click", ".role-attr", function(){
        var index = $(this).attr("table-data"),// 这个是获取到表格的第几行
            data = m.table.data()[index];// 获取到这一行的数据
        if (data) {
            console.log(data);


            $.ajax({
                    'url':'<?=Url::toRoute('attr')?>',
                    'method':'get',
                    'dataType':'application/json',
                    'data':{'type_id':data.id},
                    success:function (e) {
                        console.log(e)
                    }
                }
            )
            // 拿到数据了，做相应的处理
        }
    })
     $(function(){
         m.init();
     });
</script>
<?php $this->endBlock(); ?>