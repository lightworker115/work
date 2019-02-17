<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 18:07
 */
use jinxing\admin\AdminAsset;
use yii\helpers\json;
use yii\helpers\Url;
list(, $url) = list(, $url) = Yii::$app->assetManager->publish((new AdminAsset())->sourcePath);
$depends = ['depends' => 'jinxing\admin\AdminAsset'];
$this->registerCssFile($url . '/css/data/materialize.min.css', $depends);
$this->registerCssFile($url . '/css/data/morris-0.4.3.min.css', $depends);
$this->registerCssFile($url . '/css/data/custom-styles.css', $depends);
//$this->registerCssFile($url . '/css/data/cssCharts.css', $depends);
//分割线

$this->registerJsFile($url . '/js/data/materialize.min.js',$depends);
$this->registerJsFile($url . '/js/data/jquery.metisMenu.js',$depends);
$this->registerJsFile($url . '/js/data/raphael-2.1.0.min.js',$depends);
$this->registerJsFile($url . '/js/data/morris.js',$depends);
$this->registerJsFile($url . '/js/data/custom-scripts.js',$depends);
$this->registerJsFile($url . '/js/data/jquery.chart.js',$depends);
$this->registerJsFile($url . '/js/data/morris.data.js',$depends);
//$this->registerJsFile($url . '/js/data/echarts.min.js',$depends);
?>
<head>

    <!-- Custom CSS -->


    <style type="text/css">
        /**{*/
        /*background-color: #ffffff;*/
        /*}*/
        #page-wrapper{
            background-color: #ffffff !important;
            margin: -114px 0 0 -28px;
        }
        .card>.card-stacked>.card-content>h3{
            color: white;
        }
        b{
            padding-right: 95px;
        }

    </style>
</head>
<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<body>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="header">
            <h1 class="page-header">
                数据统计
            </h1>
        </div>
        <!--        主体部分-->
        <div id="page-inner">
            <div class="dashboard-cards">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第一个图标-->
                            <div class="card-image red">
                                <i class="material-icons dp48">shopping_cart</i>
                            </div>
                            <div class="card-stacked red">
                                <div class="card-content">
                                    <h3><?=$all?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>订单总数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第二个图标-->
                            <div class="card-image red">
                                <i class="material-icons dp48">shopping_cart</i>
                            </div>
                            <div class="card-stacked red">
                                <div class="card-content">
                                    <h3><?=$todayAll?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>今日订单数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第3个图标-->
                            <div class="card-image orange">
                                <i class="material-icons dp48">shopping_cart</i>
                            </div>
                            <div class="card-stacked orange">
                                <div class="card-content">
                                    <h3><?=$inPayNum?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>未支付订单</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第4个图标-->
                            <div class="card-image orange">
                                <i class="material-icons dp48">shopping_cart</i>
                            </div>
                            <div class="card-stacked orange">
                                <div class="card-content">
                                    <h3><?=$payNum?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>成交订单数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第5个图标-->
                            <div class="card-image green">
                                <i class="material-icons dp48">money</i>
                            </div>
                            <div class="card-stacked green">
                                <div class="card-content">
                                    <h3>￥：<?=$allPayMoney*0.01?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>已支付订单总额</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第6个图标-->
                            <div class="card-image blue">
                                <i class="material-icons dp48">money</i>
                            </div>
                            <div class="card-stacked blue">
                                <div class="card-content">
                                    <h3>￥：<?=$allUnPayMoney*0.01?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>未支付订单总额</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                    <div class="col-xs-12 col-sm-6 col-md-3">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第6个图标-->
                            <div class="card-image green">
                                <i class="material-icons dp48">money</i>
                            </div>
                            <div class="card-stacked green">
                                <div class="card-content">
                                    <h3>￥：<?=$todayPayMoney*0.01?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>今日支付订单总额</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>

                    <div class="col-xs-12 col-sm-6 col-md-3">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第6个图标-->
                            <div class="card-image blue">
                                <i class="material-icons dp48">money</i>
                            </div>
                            <div class="card-stacked blue">
                                <div class="card-content">
                                    <h3>￥：<?=$todayUnPayMoney*0.01?></h3>
                                </div>
                                <div class="card-action">
                                    <strong>今日未支付订单总额</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2"></div>
                </div>
            </div>
            <!-- /. ROW  -->
            <div class="row">
                <!--/.row-->
                <div class="col-md-12 col-xs-12 col-sm-12" style="margin-top: 100px">
                <div style="text-align: center;margin-bottom: 32px;"><span style="font-size: 30px; ">订单金额</span></div>
                <div class="card">
                    <div class="card-image">
                        <div id="morris-line-chart"></div>
                    </div>
                    <div class="card-action">
                        <b>Line Chart</b>
                    </div>
                </div>
            </div>
        </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>

</body>
<?php $this->beginBlock('javascript')?>
<script type="text/javascript">
    $.ajax({
        url:'http://'+document.domain+'/data/get-money',
        success:function (e) {
            var arr = JSON.parse(e);
            var len = arr.length;
            Morris.Line({
                element: 'morris-line-chart',
                data: [
                    { y: arr[0].date, a: arr[0].num},
                    { y: arr[1].date, a: arr[1].num},
                    { y: arr[2].date, a: arr[2].num},
                    { y: arr[3].date, a: arr[3].num},
                    { y: arr[4].date, a: arr[4].num},
                    { y: arr[5].date, a: arr[5].num}
                ],
                xkey: 'y',
                ykeys: ['a'],
                labels: ['金额:'],
                fillOpacity: 0.6,
                hideHover: 'auto',
                behaveLikeLine: true,
                resize: true,
                pointFillColors:['#ffffff'],
                pointStrokeColors: ['black'],
                lineColors:['red']

            });
        }
    });
</script>
<?php $this->endBlock();?>








