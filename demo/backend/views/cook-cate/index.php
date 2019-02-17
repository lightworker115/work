<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\Json;
// 定义标题和面包屑信息
$this->title = '菜谱类别';

?>
<?=MeTable::widget()?>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">

    var aStatus = <?=Json::encode($status)?>,
        pops = <?=Json::encode($pops)?>,
        aStatusColor = <?=Json::encode($statusColor)?>;
    var m = meTables({
        title: "菜谱类别",
        fileSelector: ["#file"],
        table: {
            "aoColumns": [
             {"title": "id", "data": "id", "edit": {"type": "hidden", }, "bSortable": false},
			{"title": "标题", "data": "title", "edit": {"type": "text", "rangelength": "[2, 50]"}, "bSortable": false}, 
			// {"title": "封面图", "data": "logo", "edit": {"type": "text", "rangelength": "[2, 255]"}, "bSortable": false},
                {
                    "title": "封面图",
                    "data": "logo",
                    "isHide": true,
                    "edit": {
                        "type": "file",
                        options: {
                            "id": "file",
                            "name": "UploadForm[logo]",
                            "input-name": "logo",
                            "input-type": "ace_file",
                            "file-name": "logo"
                        }
                    },
                    "createdCell": function (td, data) {
                        $(td).html(mt.imgString1(data));
                    },
                },
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
    var $file = null;
    mt.fn.extend({
        beforeShow: function (data) {
            console.log('进入上传模式');
            console.log($file);
            $file.ace_file_input("reset_input");
            // 修改复值
            if (this.action == "update" && !empty(data.logo)) {
                $file.ace_file_input("show_file_list", [data.logo]);
            }

            return true;
        }
    });
    $(function () {
        m.init();
        $file = $("#file");
    });

</script>
<?php $this->endBlock(); ?>