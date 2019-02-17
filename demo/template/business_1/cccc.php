<strong>
             <style>body{font-size: 25px;background: white}</style>
                     <?php foreach($arr as $value): ?>
                     <ul>
                     time()哈哈哈哈bbb
                        <li><a href="<?=\yii\helpers\Url::toRoute(["index/index","id"=>$value["id"]])?>"><?=$value["name"]?></a></li>
                     </ul>
                     <?php endforeach;?>
                 </strong>