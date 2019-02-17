<?php

use jinxing\admin\widgets\MeTable;
use jinxing\admin\AdminAsset;
use yii\helpers\Json;
// 定义标题和面包屑信息
$this->title = '跟进任务';
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];
$this->registerCssFile($url . '/css/bootstrap-datetimepicker.css', $depends);
$this->registerJsFile($url . '/js/date-time/moment.min.js', $depends);
$this->registerJsFile($url . '/js/date-time/bootstrap-datetimepicker.min.js', $depends);
?>
<?=MeTable::widget()?>

    <button class="btn btn-primary" id="back" style="margin: 20px auto;">返回上一页</button>

<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        aStatusColor = <?=Json::encode($statusColor)?>,
        card = <?=Json::encode($card)?>,
        customer = <?=Json::encode($customer)?>;
    var m = meTables({
        title: "跟进任务",
        
        table: {
            "aoColumns": [
                			{"title": "id","isHide":true, "data": "id", "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
			{
			    "title": "客户",
                "data": "record_id",
                "value": customer,
                "edit": {"type": "select", "required": true,"number": true},
                "bSortable": false,
                "createdCell": function (td, data) {
                    $(td).html(mt.valuesString(customer, '', data));
                }
            },
			{
			    "title": "员工名片",
                "data": "card_id",
                "value": card,
                "edit": {"type": "select", "required": true,"number": true},
                "bSortable": false,
                "createdCell": function (td, data) {
                    $(td).html(mt.valuesString(card, '', data));
                }
            },
			{"title": "任务名称", "data": "name", "edit": {"type": "text", "required": true,"rangelength": "[2, 255]"}, "bSortable": false},
                {
                    "title": "回访时间时间",
                    "data": "back_time",
                    "isHide": false,
                    "edit": {"type": "dateTime","class": "time-format", "required": true},
                    "bSortable": false,
                    "defaultContent": "",
                    "bViews": false,
                    "createdCell" : meTables.dateTimeString
                },
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
			{"title": "创建时间", "data": "created_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
			{"title": "更新时间", "data": "updated_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},

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
         $('.datetime-picker').datetimepicker({
             format:'YYYY-MM-D hh:mm:ss'
         });
         $('#back').click(function () {
             window.history.back();
         })


     });
</script>
<?php $this->endBlock(); ?>