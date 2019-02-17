<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Json;
use jinxing\admin\AdminAsset;
// 定义标题和面包屑信息
$this->title = '订单管理';
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];


$this->registerCssFile($url . '/css/bootstrap-editable.css', $depends);

$this->registerJsFile($url . '/js/x-editable/bootstrap-editable.min.js', $depends);
$this->registerJsFile($url . '/js/x-editable/ace-editable.min.js', $depends);
?>
    <style>
        .img{
            width: 58px;
        }
    </style>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>;
    var statusColor = <?=Json::encode($statusColor)?>,
        nickname = <?=Json::encode($nickname)?>,
        headimgurl=<?=Json::encode($headimgurl)?>;
    var m = meTables({
        title: "订单管理",
        
        table: {
            "aoColumns": [
            {"title": "自增ID","isHide":true, "data": "id", "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
			{"title": "订单号", "data": "order_num", "edit": {"type": "text", "required": true,"rangelength": "[2, 20]"}, "search": {"type": "text"}, "bSortable": false},
			{"title": "openid", "data": "openid","bViews":false,"edit": {"type": "text", "required": true,"number": true},"isHide":true, "bSortable": false},
                {"title": "头像", "data": "openid",
                    "createdCell": function (td, data) {
                        $(td).html(mt.imgString(headimgurl,data,''));
                    },
                    "bSortable": false
                },
			{"title": "商品名", "data": "goods_name", "edit": {"type": "text", "required": true}, "bSortable": false},
                {"title": "商品规格", "data": "product_name", "bSortable": false},
//			{"title": "运费", "data": "freight", "edit": {"type": "text", "required": true}, "bSortable": false},
			{"title": "订单总价", "data": "total_money", "edit": {"type": "text", "required": true}, "bSortable": false,
                "createdCell": function (td, data) {
                        $(td).html('<span>'+data*0.01+'</span>');
                    }
            },
			{"title": "实际订单总价", "data": "real_total_money", "edit": {"type": "text" }, "bSortable": false,
                "createdCell": function (td, data) {
                    $(td).html('<span>'+data*0.01+'</span>');
                }
            },
			{"title": "收货人姓名", "data": "user_name", "edit": {"type": "text", "rangelength": "[2, 20]"}, "bSortable": false},
			{"title": "收货地址", "data": "user_address", "edit": {"type": "text", "rangelength": "[2, 255]"}, "bSortable": false},
			{"title": "收件人手机", "data": "user_phone", "edit": {"type": "text" }, "bSortable": false},
			{"title": "订单备注", "data": "remarks", "edit": {"type": "text", "rangelength": "[2, 255]"}, "bSortable": false},
                {
                    "title": "状态",
                    "data": "status",
                    "value": aStatus,
                    "edit": {"type": "radio", "default": 0, "required": true, "number": true},
                    "search": {"type": "select"},
                    "bSortable": false,
                    "editable": {
                        "type": "select"
                    },
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aStatus, statusColor, data));
                    }
                },
                {"title": "支付时间", "data": "pay_at", "edit": {"type": "text", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
			{"title": "下单时间", "data": "created_at", "edit": {"type": "text", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString}, 
			{"title": "更新时间", "data": "updated_at", "edit": {"type": "text", "number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString}, 

            ]
        },
        operations: {
            width: "200px", // 这列的宽度
            buttons: {
                update:{bShow:false},
                delete:{bShow:false}
                // 其他按钮配置

            }
        },
        buttons:{
            "create":{
                "bShow":false
            },
            "updateAll":{
                "bShow":false
            },
            "deleteAll":{
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
</script>
<?php $this->endBlock(); ?>