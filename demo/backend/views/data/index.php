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
$this->registerCssFile($url . '/css/data/cssCharts.css', $depends);
//分割线

$this->registerJsFile($url . '/js/data/materialize.min.js',$depends);
$this->registerJsFile($url . '/js/data/jquery.metisMenu.js',$depends);
$this->registerJsFile($url . '/js/data/raphael-2.1.0.min.js',$depends);
$this->registerJsFile($url . '/js/data/morris.js',$depends);
$this->registerJsFile($url . '/js/data/custom-scripts.js',$depends);
$this->registerJsFile($url . '/js/data/jquery.chart.js',$depends);
$this->registerJsFile($url . '/js/data/morris.data.js',$depends);
$this->registerJsFile($url . '/js/data/echarts.min.js',$depends);
?>
<head>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <!-- Custom CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <script>
    </script>
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
        .funnel{
            width: 850px;
            height: 650px;
            margin: 0 auto;
        }
        .progress {
            margin: 0 auto;
            width: 490px;
        }

        .progress {
            padding: 4px;
            background: #b1dcfb;
            border-radius: 6px;
            -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25), 0 1px rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25), 0 1px rgba(255, 255, 255, 0.08);
        }

        .progress-bar {
            position: relative;
            height: 16px;
            border-radius: 4px;
            -webkit-transition: 0.4s linear;
            -moz-transition: 0.4s linear;
            -o-transition: 0.4s linear;
            transition: 0.4s linear;
            -webkit-transition-property: width, background-color;
            -moz-transition-property: width, background-color;
            -o-transition-property: width, background-color;
            transition-property: width, background-color;
            -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
        }
        .progress-bar:before, .progress-bar:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
        }
        .progress-bar:before {
            bottom: 0;
            border-radius: 4px 4px 0 0;
        }
        .progress-bar:after {
            z-index: 2;
            bottom: 45%;
            border-radius: 4px;
            background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: -moz-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: -o-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
        }
        .bar{
            margin-left: -232px;
        }
        .text{
            margin-top: 50px;
            margin-left: 746px;
            margin-bottom: 10px;
            width: 600px;
        }
        .text>span{
            font-size: 20px;
            color: dimgrey ;
        }
        .newpercent{
            margin-left: 262px;
            font-size: 38px !important;
            color: blue !important;
        }
    </style>
</head>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<body>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="header">
            <h1 class="page-header">
                数据统计
            </h1>
        </div>
<!--        日期选择框-->
        <div >
            <select name="date" id="date" class="form-control input-sm" style="width: 200px">
                <option value="0" selected>总计</option>
                <option value="1">今天</option>
                <option value="2">昨天</option>
                <option value="3" >上周</option>
                <option value="4">这周</option>
                <option value="5">上个月</option>
                <option value="6">这个月</option>
            </select>
        </div>


<!--        主体部分-->
        <div id="page-inner">
            <div class="dashboard-cards">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-2">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
<!--                            第一个图标-->
                            <div class="card-image red">
                                <i class="material-icons dp48">import_export</i>
                            </div>
                            <div class="card-stacked red">
                                <div class="card-content">
                                    <h3 id="usernum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>客户总数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <!--                            第二个图标-->
                            <div class="card-image orange">
                                <i class="material-icons dp48">shopping_cart</i>
                            </div>
                            <div class="card-stacked orange">
                                <div class="card-content">
                                    <h3 id="tasknum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>跟进总数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
<!--                            第三个图标-->
                            <div class="card-image blue">
                                <i class="material-icons dp48">equalizer</i>
                            </div>
                            <div class="card-stacked blue">
                                <div class="card-content">
                                    <h3 id="seenum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>浏览总数</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2">

                        <div class="card horizontal cardIcon waves-effect waves-dark">
<!--                            第四个图标-->
                            <div class="card-image green">
                                <i class="material-icons dp48">supervisor_account</i>
                            </div>
                            <div class="card-stacked green">
                                <div class="card-content">
                                    <h3 id="sharenum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>被转发总数</strong>
                                </div>
                            </div>
                        </div>

                    </div>
<!--                    第五个图标-->
                    <div class="col-xs-12 col-sm-6 col-md-2">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <div class="card-image purple">
                                <i class="material-icons dp48">supervisor_account</i>
                            </div>
                            <div class="card-stacked purple">
                                <div class="card-content">
                                    <h3 id="savenum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>被保存总数</strong>
                                </div>
                            </div>
                        </div>

                    </div>
<!--                    第六个图标-->
                    <div class="col-xs-12 col-sm-6 col-md-2">
                        <div class="card horizontal cardIcon waves-effect waves-dark">
                            <div class="card-image pink">
                                <i class="material-icons dp48">supervisor_account</i>
                            </div>
                            <div class="card-stacked pink">
                                <div class="card-content">
                                    <h3 id="fabulousnum"></h3>
                                </div>
                                <div class="card-action">
                                    <strong>被点赞总数</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /. ROW  -->
            <div class="row">

                <!--/.row-->
<!--                右边图表-->
                <div class="col-xs-12 col-sm-12 col-md-5">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card">
                                <div class="card-image donutpad">
                                    <div id="morris-donut-chart"></div>
                                </div>
                                <div class="card-action">
                                    <span class="fa fa-circle-o fa-2x" style="color: red"></span>
                                    <b>对我感兴趣</b>
                                    <span class="fa fa-circle-o fa-2x" style="color: green"></span>
                                    <b>对产品感兴趣</b>
                                    <span class="fa fa-circle-o fa-2x" style="color: blue"></span>
                                    <b>对公司感兴趣</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.row-->
                <div class="col-md-7 col-xs-12 col-sm-12">
                    <div style="text-align: center;margin-bottom: 32px;"><span style="font-size: 30px; ">客户活跃</span></div>
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


            <div class="row">

            </div>
            <div class="row">
                <div class="col-md-7 col-xs-12 " style="margin-left: 265px;">
                    <div class="funnel" id="funnel" ></div>
                </div>

                <div class="col-md-3" style="height: 660px;">
                    <div class="loudou" style="margin-top: 98px;margin-left: -167px;font-size: 18px;color:darkgrey"><span class="index_part3font">全部咨询 </span><span class="index_part3number" id="all"> 人</span></div>
                    <div class="loudou" style="margin-top: 80px; margin-left: -228px;font-size: 18px;color:darkgrey"><span class="index_part3font">待跟进 </span><span class="index_part3number" id="wait">人</span></div>
                    <div class="loudou" style=" margin-top:79px;margin-left: -286px;font-size: 18px;color:darkgrey"><span class="index_part3font">跟进中</span><span class="index_part3number" id="following">人</span></div>
                    <div class="loudou" style="margin-top: 79px;margin-left: -351px;font-size: 18px;color:darkgrey"><span class="index_part3font">已邀约</span> <span class="index_part3number" id="invite">人</span></div>
                    <div class="loudou" style="margin-top: 80px;margin-left: -411px;font-size: 18px;color:darkgrey"><span class="index_part3font">已成交</span> <span class="index_part3number" id="complete"> 人</span></div>
                </div>

            <div class="bar">
                <div class="text"><span>成交率 (已成交/全部咨询)</span><span class="newpercent" id="success">%</span></div>
                <div class="progress ">
                    <div class="progress-bar suc"></div>
                </div>
                <div class="text"><span>失效率 (已失效/全部咨询)</span><span class="newpercent" id="fail">%</span></div>
                <div class="progress ">
                    <div class="progress-bar lose"></div>
                </div>
            </div>
            </div>
        <!-- /. PAGE INNER  -->
    </div>
    <!-- /. PAGE WRAPPER  -->
</div>

</body>
<?php $this->beginBlock('javascript')?>
<script >
//默认值
    $.ajax({
        url:'http://'+document.domain+'/data/get-index-num',
        data:{'date':0,'eid':<?=$enterprise_id?>},
        success:function (e) {
            var arr = JSON.parse(e);
//            进行一系列的动态赋值
            $('#usernum').html(arr['usernum']);
            $('#tasknum').html(arr['tasknum']);
            $('#seenum').html(arr['seenum']);
            $('#sharenum').html(arr['sharenum']);
            $('#savenum').html(arr['savenum']);
            $('#fabulousnum').html(arr['fabulousnum']);
            $('#all').html('<span>'+arr['all']+'人'+'</span>');
            $('#wait').html('<span>'+arr['wait']+'人'+'</span>');
            $('#following').html('<span>'+arr['following']+'人'+'</span>');
            $('#invite').html('<span>'+arr['invite']+'人'+'</span>');
            $('#complete').html('<span>'+arr['complete']+'人'+'</span>');
            $('#success').html('<span>'+arr['success']+'%'+'</span>');
            $('#fail').html('<span>'+arr['fail']+'%'+'</span>');
        }
    });
//折线图
    $.ajax({
    url:'http://'+document.domain+'/data/get-coustomer',
    data:{'date':0},
    success:function (e) {
        var arr = JSON.parse(e);
        var len = arr.length;
        Morris.Line({
            element: 'morris-line-chart',
            data: [
                { y: arr[0].date, a: arr[0].num,b:arr[0].numforpro,c:arr[0].numforcom},
                { y: arr[1].date, a: arr[1].num,b:arr[1].numforpro,c:arr[1].numforcom},
                { y: arr[2].date, a: arr[2].num,b:arr[2].numforpro,c:arr[2].numforcom},
                { y: arr[3].date, a: arr[3].num,b:arr[3].numforpro,c:arr[3].numforcom},
                { y: arr[4].date, a: arr[4].num,b:arr[4].numforpro,c:arr[4].numforcom},
                { y: arr[5].date, a: arr[5].num,b:arr[5].numforpro,c:arr[5].numforcom}
            ],
            xkey: 'y',
            ykeys: ['a','b','c'],
            labels: ['对我感兴趣:','对产品感兴趣:','对公司感兴趣'],
            fillOpacity: 0.6,
            hideHover: 'auto',
            behaveLikeLine: true,
            resize: true,
            pointFillColors:['#ffffff'],
            pointStrokeColors: ['black'],
            lineColors:['red','green','blue']

        });
    }
});
//    监听下拉框选中事件
    $('#date').on('change',function () {
//            获取选中值
        var value = $(this).val();
//        根据值去发送请求获取数据
        $.ajax({
            url:'http://'+document.domain+'/data/get-data',
            data:{'date':value},
            success:function (e) {
                var d = JSON.parse(e);
                var a = d[0].followme;
                var b = d[0].followcompany;
                var c = d[0].followproduct;
                var donut = new Morris.Donut({
                    element: 'morris-donut-chart',
                    resize: true,
                    colors: ["#3c8dbc", "#f56954", "#00a65a"],
                    data: [
                        {label: "对公司感兴趣", value: b},
                        {label: "对产品感兴趣", value: c},
                        {label: "对我感兴趣", value: a}
                    ],
                    hideHover: 'true'
                });
            }
        }); //饼图
//        数据
        $.ajax({
            url:'http://'+document.domain+'/data/get-index-num',
            data:{'date':$(this).val(),'eid':<?=$enterprise_id?>},
            success:function (e) {
                var arr = JSON.parse(e);
                $('#usernum').html(arr['usernum']);
                $('#tasknum').html(arr['tasknum']);
                $('#seenum').html(arr['seenum']);
                $('#sharenum').html(arr['sharenum']);
                $('#savenum').html(arr['savenum']);
                $('#fabulousnum').html(arr['fabulousnum']);
                $('#all').html('<span>'+arr['all']+'人'+'</span>');
                $('#wait').html('<span>'+arr['wait']+'人'+'</span>');
                $('#following').html('<span >'+arr['following']+'人'+'</span>');
                $('#invite').html('<span>'+arr['invite']+'人'+'</span>');
                $('#complete').html('<span>'+arr['complete']+'人'+'</span>');
                $('#success').html('<span>'+arr['success']+'%'+'</span>');
                $('#fail').html('<span>'+arr['fail']+'%'+'</span>');
            }
        });
//        删除原来的折线图
        $('#morris-line-chart>svg').remove();
        $('#morris-line-chart>div.morris-hover').remove();
        //折线图
        $.ajax({
            url:'http://'+document.domain+'/data/get-coustomer',
            method:'get',
            data:{'date':value},
            success:function (e) {
                var arr = JSON.parse(e);
                var len = arr.length;
                Morris.Line({
                    element: 'morris-line-chart',
                    data: [
                        { y: arr[0].date, a: arr[0].num,b:arr[0].numforpro,c:arr[0].numforcom},
                        { y: arr[1].date, a: arr[1].num,b:arr[1].numforpro,c:arr[1].numforcom},
                        { y: arr[2].date, a: arr[2].num,b:arr[2].numforpro,c:arr[2].numforcom},
                        { y: arr[3].date, a: arr[3].num,b:arr[3].numforpro,c:arr[3].numforcom},
                        { y: arr[4].date, a: arr[4].num,b:arr[4].numforpro,c:arr[4].numforcom},
                        { y: arr[5].date, a: arr[5].num,b:arr[5].numforpro,c:arr[5].numforcom}
                    ],
                    xkey: 'y',
                    ykeys: ['a','b','c'],
                    labels: ['对我感兴趣:','对产品感兴趣:','对公司感兴趣'],
                    fillOpacity: 0.6,
                    hideHover: 'auto',
                    behaveLikeLine: true,
                    resize: true,
                    pointFillColors:['#ffffff'],
                    pointStrokeColors: ['black'],
                    lineColors:['red','green','blue']
                });
            }
        });
    });
    $.ajax({
        url:'http://'+document.domain+'/data/get-data',
        data:{'date':0},
        success:function (e) {
            var d = JSON.parse(e);
            var a = d[0].followme;
            var b = d[0].followcompany;
            var c = d[0].followproduct;
            var donut = new Morris.Donut({
                element: 'morris-donut-chart',
                resize: true,
                colors: ["#3c8dbc", "#f56954", "#00a65a"],
                data: [
                    {label: "对公司感兴趣", value: b},
                    {label: "对产品感兴趣", value: c},
                    {label: "对名片感兴趣", value: a}
                ],
                hideHover: 'true'
            });
        }
    }); //饼图

            var funnel =echarts.init(document.getElementById('funnel'));
            var option = {
                title: {
                    text: '成交率漏斗',
                    padding:[0,400],
                    textStyle:{
                        fontFamily:'SimSun',
                        fontSize:30
                    }
                },
                tooltip: {
                    show:false,
                    trigger: 'item',
                    formatter: "{b} : {c}"
                },
                toolbox: {
                    show:false,
                    feature: {
                        dataView: {readOnly: false},
                        restore: {},
                        saveAsImage: {}
                    }
                },
                legend: {
                    show:false
                },
                calculable: true,
                // 金字塔块的颜色
                color: ['#f66894','#fac666','#b794e6','#33d8ad','#3489df'],
                series: [
                    {
                        name:'成交人数',
                        type:'funnel',
                        left: '10%',
                        top: 60,
                        //x2: 80,
                        bottom: 60,
                        width: '80%',
                        // height: {totalHeight} - y - y2,
                        min: 0,
                        max: 100,
                        minSize: '0%',
                        maxSize: '90%',
                        sort: 'descending',
                        gap: 2,
                        label: {
                            normal: {
                                show: true,
                                formatter: '',
                                position: 'outline',
                                length:100
                            },
                            emphasis: {
                                textStyle: {
                                    fontSize: 16
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show:true,
                                length: 40,
                                lineStyle: {
                                    width:1,
                                    type: 'solid'
                                }
                            }
                        },
                        itemStyle: {
                            normal: {
                                borderColor: '#fff',
                                borderWidth: 1
                            }
                        },
                        data: [
                            {value: 20, name: '已成交'},
                            {value: 40, name: '已邀约'},
                            {value: 60, name: '跟进中'},
                            {value: 80, name: '待跟进'},
                            {value: 100, name: '全部咨询'}
                        ]
                    }
                ]
            };
            funnel.setOption(option);
//进度条
     var suc =  $('#success').val(),
         fai =  $('#fail').val();
    $('.suc').css('width',suc+'%');
    $('.lose').css('width',fai+'%');
</script>
<?php $this->endBlock();?>








