<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/29
 * Time: 15:06
 */
$this->title = '员工排行';
?>
<style type="text/css">
    .alert{
        padding: 0;
        position: relative;
    }
    .alert-info{
        background-color: #ffffff;
        height: 82px;
        line-height: 82px;
    }
    .name{
        font-size: 31px;
        margin-left: 24px;
    }
    .num{
        position: absolute;
        right:50px;
        font-size: 31px;
    }
    .img{
        border: 0.6px solid #e8e8e8;
        border-radius: 50%;
        width: 62px;
        height: 62px;
        margin-top: -20px;
        margin-left: 41px;
    }
</style>
<div class="container">
    <?php foreach ($data as $data):?>
    <div class="alert alert-info">
        <img src="<?=$data->portrait?>"  class="img">
        <span class="name"><?=$data->name?></span>
        <span class="num"><?=$data->deal_num?></span>
    </div>
    <?php endforeach;?>
</div>
