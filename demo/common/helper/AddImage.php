<?php
/**
 * @Description 微信多图片上传
 * @Date: 2016.4.19
 * @author congrong.wang <email:congrong512@vip.qq.com>
 * @since 1.0.0
 * 实现微信的多图片上传
 * 在需要实现多图片上传的页面中 添加隐藏域 value值设置要上传的图片给个数(不超过9张)
 * 例如: <input type="hidden" id="wechat_upload_img_no" value="3">
 * 并且引入当前文件即可
 */
$_csrf = \Yii::$app->request->csrfToken;
$upload_img_url = \yii\helpers\Url::toRoute(['/helper/upload']);
?>
    <style>
        .upload_img_list{
            position: relative;
        }
        .file-content{
            overflow: hidden;
            padding: 5px 0;
        }
        .upload_img_list i{
            position: absolute;
            right: -5px;
            top: -5px;
            width: 20px;
            height: 20px;
            text-align: center;
            background: rgba(0,0,0,0.8);
            border-radius: 100%;
            color: white;
        }
        .file-content div {
            display: inline-block;
            position: relative;
            margin-right: 6px;
            width: 70px;
            height: 70px;
            border: 1px solid #e8e8e8;
            float: left;
        }
        .file-content div img{
            width: 100%;
            height: 100%;
        }
    </style>
    <div class="file-content" id="upload_img">
        <div class="file-input" id="upload_btn" >
<!--            <img src="images/seckill/file-img.png"/>-->
<!--            <label>0/8</label>-->
        </div>
    </div>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(<?php echo $jsSdk->buildConfig(array('chooseImage', 'uploadImage', 'downloadImage'), false) ?>);
</script>
<?php
$javascript = <<<JS
     $('#upload_btn').click(function(){
          var than = $(this);
          var upload_url = "$upload_img_url";
          var no =$('#wechat_upload_img_no').val();
              upload_img(than,upload_url,no);
          })
   function upload_img(obj,url,no){
      var img =obj.find('img');
      var images = {
        localId: [],
        serverId: []
      };
        var that =$(this);
        images.localId = [];
        wx.chooseImage({
          count: no, // 默认9
          success: function (res) {
            images.localId = res.localIds;
            if (images.localId.length == 0) {
                alert('请先使用 chooseImage 接口选择图片');
                return;
            }
            if(images.localId.length > no) {
                alert('目前仅支持'+no+'张图片上传');
                images.localId = [];
                return;
            }
            var i = 0, length = images.localId.length;
            images.serverId = [];
            function upload() {
                         wx.uploadImage({
                                    localId: images.localId[i],
                                    success: function (res) {
                                     
                          var str ="";
                              str = '<div class="upload_img_list"><i onclick=delteImg(this,'+no+')>×</i>'
                              str +='<img src='+images.localId[i]+' /></div>'
                                        obj.parent('div').prepend(str);
                                        images.serverId.push(res.serverId);
                                       
                                        $.ajax({
                                        url:url,
                                        type:"POST",
                                        data:{mediaId:images.serverId[i],_csrf:"$_csrf",business_id:$business_id},
                                        success:function(date){
                                           var input="";
                                               input ='<input type="hidden" name="image[]" value='+ date +'>'
                                               obj.append(input);
                                        },
                                        error:function(){
                                          alert("上传失败")
                                        }
                                        })
                                        if(obj.parent('div').find('div').length >no){
                                            obj.css('display','none');
                                            return false;
                                        }

                                        i++;

                                        if (i < length) {
                                                upload();
                                        }
                                    },
                                    fail: function (res) {
                                                alert(JSON.stringify(res));
                                     }
                          });
            }
            upload();
          }
        });

     }

     function delteImg(obj,no){
        $(obj).parent('.upload_img_list').remove();
        if( $(obj).parents('#upload_img').find('.upload_img_list').length <no){
           $('#upload_btn').css('display','block');
            return false;
          }

     }
JS;
$this->registerJs($javascript, \yii\web\View::POS_END);
?>