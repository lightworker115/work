<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Json;
// 定义标题和面包屑信息
$this->title = '用户操作记录';
?>
    <style>
        .img{
            width: 66px;
            height: 48px;
            text-align: center;
        }
    </style>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var zOperation = <?=Json::encode($operation)?>,
        aPlace = <?=Json::encode($place)?>,
        card = <?=Json::encode($card)?>,
        customer = <?=Json::encode($customer)?>,
        nickname = <?=Json::encode($nickname,JSON_FORCE_OBJECT)?>,
        headimgurl = <?=Json::encode($headimgurl,JSON_FORCE_OBJECT)?>;
    var m = meTables({
        title: "用户操作记录",
        table: {
            "aoColumns": [
            {"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
			{"title": "enterprise_id", "data": "enterprise_id","isHide":true, "edit": {"type": "text", "required": true,"number": true}, "bSortable": false,"bViews":false},
                {"title": "用户头像","data":"openid","value":headimgurl, "bSortable": false,"bViews":false,"createdCell": function (td,data) {
                    $(td).html(mt.imgString(headimgurl,data));
                }},
                {
			    "title": "员工",
                "data": "card_id",
                "value":card,
                "edit": {"type": "select", "required": true,"number": true},
                "bSortable": false,
                "createdCell": function (td, data) {
                            $(td).html(mt.valuesString1(card, '', data));
                            }
            },
            {
                "title": "客户",
                "data": "record_id",
                "value": customer,
                "isHide":true,
                "bViews":false,
                "edit": {"type": "select", "required": true,"number": true},
                "bSortable": false,
                "createdCell": function (td, data) {
                    $(td).html(mt.valuesString1(customer, '', data));
                }
            },

			{"title": "客户昵称","data":"openid","value":nickname, "bSortable": false,"createdCell": function (td,data) {
                $(td).html(mt.valuesString1(nickname, '', data))
            }},


                {
                    "title": "状态",
                    "data": "operation_type",
                    "value": zOperation,
                    "edit": {"type": "select", "default": 1, "required": true, "number": true},
                    "search": {"type": "select"},
                    "bSortable": false,
                    "editable": {
                        "type": "select"
                    },
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(zOperation, '', data));
                    }
                },
                {
                    "title": "地方",
                    "data": "place",
                    "value": aPlace,
                    "isHide": false,
                    "edit": {"type": "select", "default": 1, "required": true, "number": true},
                    "bSortable": false,
//                    "search": {"type": "select"},
                    "createdCell": function (td, data) {
                        $(td).html(mt.valuesString(aPlace,'',data));
                    }
                },
			{"title": "操作次数", "data": "frequency", "edit": {"type": "text", "required": true,"number": true}, "bSortable": false}, 
			{"title": "创建时间", "data": "created_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},
			{"title": "更新时间", "data": "updated_at", "edit": {"type": "hidden", "required": true,"number": true}, "bSortable": false, "createdCell" : meTables.dateTimeString},

            ]       
        },
        operations:{
                buttons:{
                    update:{bShow:false},
                    delete:{bShow:false}
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