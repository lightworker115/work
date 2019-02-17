<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/4
 * Time: 22:58
 */
$this->title='文章';
?>
<style>
    #article{
        margin-top: 30px;
        width: 820px;
        height: 192px;
        border: 0.6px solid #e8e8e8;
        float: left;

    }
    .img{
        margin: 20px 0;
        width: 220px;
        height: 150px;
        border: 0.6px solid #E8E8E8;
        float: left;
    }
    .text{
        margin: 30px 0;
        width: 543px;
        height: 110px;
        border: 0.6px solid #E8E8E8;
        float: right;

    }
    #list{
        margin-top: -10px;
        width: 310px;
        height: 963px;
        border: 0.6px solid #E8E8E8;
        float: right;
    }
    .logo{
        width: 100px;
        height: 86px;
        float: left;
        border: 0.6px solid #E8E8E8;
    }
    .font{
        width: 200px;
        height: 66px;
        float: right;
        border: 0.6px solid #E8E8E8;
    }
</style>
<!--    轮播图-->
<div id="myCarousel" class="carousel slide">
    <!-- 轮播（Carousel）指标 -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <!-- 轮播（Carousel）项目 -->
    <div class="carousel-inner">
        <div class="item active">
            <img src="../../image/swiper.jpg" alt="First slide">
        </div>
        <div class="item">
            <img src="../../image/swiper.jpg" alt="Second slide">
        </div>
        <div class="item">
            <img src="../../image/swiper.jpg" alt="Third slide">
        </div>
    </div>
    <!-- 轮播（Carousel）导航 -->
    <a class="carousel-control left" href="#myCarousel"
       data-slide="prev"> <span _ngcontent-c3="" aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span></a>
    <a class="carousel-control right" href="#myCarousel"
       data-slide="next">&rsaquo;</a>
</div>
<!--  文章页面-->
<div id="article" >
    <div class="img">
        <img src="" alt="">
    </div>
    <div class="text">
        文字
    </div>
</div>
<h3 style="width: 310px;height: 40px;background-color:#00b8d4;line-height:40px;float: right;border: 0.6px solid #e8e8e8">最新评论文章</h3>
<div id="list">
    <div class="logo">
        <img src="" alt="">
    </div>
    <div class="font">
        <h5>sss</h5>
        <h5>sss</h5>
    </div>
</div>