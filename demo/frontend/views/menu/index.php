<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/4
 * Time: 21:01
 */
$this->title='菜谱';
?>
<?php $this->registerCssFile("@web/css/sort/main.css")?>
<?php $this->registerCssFile("@web/css/sort/sortable.min.css")?>
<div class="container">
    <main class="sortable">
        <div class="container">
            <div class="wrapper">
                <ul class="sortable__nav nav">
                    <li style="background: #e8e8e8">
                        <a data-sjslink="all" class="nav__link">
                            所有
                        </a>
                    </li>
                    <li style="background: #e8e8e8">
                        <a data-sjslink="love" class="nav__link">
                            推荐分类
                        </a>
                    </li>
                </ul>
                <div id="sortable" class="sjs-default">
                    <?php foreach ($love as $item):?>
                    <div data-sjsel="love">
                        <div class="card">
                            <a href="<?=\yii\helpers\Url::toRoute(['cate','id'=>$item['id']])?>"><img class="card__picture" src="<?="http://admin.cook.com".$item['logo']?>" alt=""></a>
                            <div class="card-infos">
                                <h2 class="card__title"><?=$item['title']?></h2>
                                <p class="card__text">
                                    <?=$item['title']?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;?>
                    <?php foreach ($all as $item):?>
                        <div data-sjsel="all">
                            <div class="card">
                                <a href="#"><img class="card__picture" src="<?="http://admin.cook.com".$item['logo']?>" alt=""></a>
                                <div class="card-infos">
                                    <h2 class="card__title"><?=$item['title']?></h2>
                                    <p class="card__text">
                                        <?=$item['title']?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $this->registerJsFile("@web/js/sort/sortable.min.js",["position"=>\yii\web\View::POS_HEAD])?>
<script>
    document.querySelector('#sortable').sortablejs();
</script>
