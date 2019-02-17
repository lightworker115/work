<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 11:15
 */
$this->title='客户操作记录';
?>
<style>
    .alert{
        text-align: center;
        font-size: 16px;
    }
    #back{
        margin-left:791px;
    }
</style>
<div class="alert alert-danger">还没有该用户的记录</div>
<button class="btn btn-default" id="back">返回上一页</button>
<script type="text/javascript">
    document.getElementById('back').onclick=function () {
        window.history.back()
    }
</script>