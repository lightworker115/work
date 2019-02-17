<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 13:49
 */
$this->title = '密码修改';
?>
<form  id="myForm" action="<?=\yii\helpers\Url::toRoute('data')?>" method="get" onsubmit="return valid()">
    <div class="form-group">
        <label class=" control-label div-left-username"> 管理员账号 </label>
        <div class=" div-right-username">
            <input type="text" required="true" rangelength="[2, 255]" name="username" class="form-control" value="<?=$username?>">
        </div>
    </div>
    <div class="form-group">
        <label class=" control-label div-left-password"> 密码 </label>
        <div class=" div-right-password">
            <input id="pwd" type="password" rangelength="[2, 20]" name="password" class="form-control " required minlength="6" maxlength="20">
        </div>
    </div>
    <div class="form-group">
        <label class=" control-label div-left-repassword"> 确认密码 </label>
        <div class="div-right-repassword">
            <input id="repwd" type="password" rangelength="[2, 20]" equalto="input[name=password]:first" name="repassword" class="form-control " required>
        </div>
        <text id="warn" style="color: red;">请输入相同的密码</text>
    </div>
    <div>
         <button type="submit" class="btn btn-default">提交</button>
         <button type="reset" class="btn btn-danger">重置</button>
    </div>
</form>
<?php $this->beginBlock('javascript')?>
<script language="javascript">
    $('#warn').hide();
    function valid() {
        var pwd = $('#pwd').val();
        var repwd = $('#repwd').val();
        if (pwd != repwd){
            $('#warn').show();
            return false;
        }
    }
</script>
<?php $this->endBlock();?>
