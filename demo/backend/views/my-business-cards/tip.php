<?php
use yii\helpers\Url;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 15:32
 */
$this->title="个人名片";
?>
<div class="container">`
    <div class="alert alert-warning">
        <a href="<?=Url::toRoute('business-cards/index')?>" class="alert-link">警告！管理员请去员工名片查看。
        <span class="close" data-dismiss="alert">&times;</span>
        </a>
    </div>
</div>
