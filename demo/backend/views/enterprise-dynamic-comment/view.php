<?php

use jinxing\admin\widgets\MeTable;
use yii\helpers\json;
// 定义标题和面包屑信息
$this->title = '动态评论';
?>
<?=MeTable::widget()?>

    <button class="btn btn-primary" id="back" style="margin: 20px auto;">返回上一页</button>

<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        var m = meTables({
            title: "动态评论",
            param:{
                id: 2
            },
            table: {
                "aoColumns": [
                    {"title": "id", "data": "id","isHide":true, "edit": {"type": "hidden" }, "bSortable": false,"bViews":false},
//                    {"title": "企业id", "data": "enterprise_id", "edit": {"type": "text", "required": true,"number": true}, "bSortable": false},
//                    {"title": "openid", "data": "openid", "edit": {"type": "hidden", "required": true}, "bSortable": false,"bShow":false},
//                    {"title": "用户名", "data": "openid", "value":'', "bSortable": false,"createdCell": function (td) {
//                        $(td).html();
//                    }},
//                    {"title": "用户头像", "data": "openid", "value":'', "bSortable": false,"createdCell": function (td) {
//                        $(td).html('<img src="" style="height:39px;width:39px;border:1px solid #e8e8e8;">');
//                    }},
                    {"title": "评论内容", "data": "comment",  "edit": {"type": "textarea", "rows": 6, "required": true, "rangelength": "[2, 255]"}, "bSortable": false},
                    {"title": "发表时间", "data": "created_at",  "sName": "created_at", "createdCell" : meTables.dateTimeString},
                    {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : meTables.dateTimeString},

                ]
            },
            operations:{
                buttons:{
                    update:{bShow:false}
                }
            },
            buttons:{
                "create":{bShow:false},
                "updateAll":{bShow:false}
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
            $("#back").click(function () {
                window.history.back();
            })
        });
    </script>
<?php $this->endBlock(); ?>