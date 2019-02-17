<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/11/27
 * Time: 20:52
 */
  $this->title = '美食网站首页';
?>
<style>
    *{
        margin: 0;
        padding: 0;

    }
    #recomend{
        margin-top: 52px;
        height: 652px;
    }
    #recomend h3{
        color: #0a6aa1;
    }
    .cover{
        width: 220px;
        height: 278px;
        margin-right:64px ;
        margin-top: 20px;
        border: 0.4px solid #00a0e9;
    }
    .pic{
        display: block;
        width: 220px;
        height: 220px;
    }
    #article{
        position: absolute;
        top: 20px;
    }
    .main{
        font-size: 16px;
    }
    .suggest{
        display: inline-block;
    }

    .suggest img{
        display: block;
        width: 140px;
        height: 110px;
        border: 0.6px solid grey;
    }
    #notice{
        width: 300px;
        height: 256px;
        border: 0.6px solid red;
        position: fixed;
        top: 385px;
        right: 20px;
    }
</style>
<div class="container" >
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
<!--    公告栏-->
    <div id="notice">
        <h4>公告</h4>
    </div>
<!--    菜谱推荐-->
    <div id="recomend">
        <h3>每日推荐菜谱</h3>
<!--        第一列-->
        <div class="first">
            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>
        </div>
<!--        第二列-->
        <div class="second">
            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>

            <div class="col-md-2 cover">
                <a class="pic">
                    <img src="" alt="">
                </a>
                <a>sssss</a>
            </div>
        </div>
    </div>
<!--    精选文章-->
    <div class="article">
        <div style="margin-top: 20px">
            <h3 style="color: #a5933385">精选文章</h3>
        </div>
<!--        推荐位-->
        <div class="onehot clearfix">
            <a href="#" target="_blank"><img src="" alt=""></a>
            <h3><a target="_blank" href="/article/detail/4052">欢乐欧洲“哥瑞纳帕达诺奶酪&amp;帕尔玛火腿”尽享意式盛宴上海媒体午宴顺利举办</a></h3>
            <p></p>
        </div>
        <ol class="main">
            <li><a href="">first</a></li>
            <li><a href="">second</a></li>
            <li><a href="">third</a></li>
            <li><a href="">fourth</a></li>
            <li><a href="">five</a></li>
            <li><a href="">six</a></li>
        </ol>
    </div>
</div>
<div class="newer clearfix">
    <div class="focont">
        <div class="dgintro">
            <div class="logo2">图片logo</div>
            <div class="dgifo">
                <h5>豆果美食，龙岩美食菜谱分享网站。</h5>
                <p style="width:637px;"><span>1,000,000</span>道菜谱，<span>15,625,000</span>条美食日记，<span>50,000,000</span>美食达人，每天都有新分享。</p>
            </div>
        </div>

    </div>
</div>
