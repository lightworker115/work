<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 17:33
 */
$this->title = '跳转页';
?>
<?php if ($status == 'suc'):?>
    <div style="margin: 0 auto;font-size: 20px;color: green;">
        <?=$html?>
    </div>
<?php endif;?>
<?php if ($status == 'fail'):?>
    <div style="margin: 0 auto;font-size: 20px;">
        <?=$html?>
    </div>
<?php endif;?>
<script>
    setTimeout(function temp(){
        window.location.href = 'http://'+document.domain+'/'+'<?=$url?>';
        }
        ,<?=$sec?>)
</script>


