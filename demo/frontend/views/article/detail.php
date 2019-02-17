<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/5
 * Time: 20:48
 */
$this->title='详情';
?>
<style>
    .head{
        border: 0.6px solid #E8E8E8;
    }
    .comment{
        margin-top: 20px;
        border: 0.6px solid #E8E8E8;
    }
</style>
<!--标题正文-->
<div class="col-md-12 head">
    <h1 style="text-align: center">这是标题</h1>
    <div class="col-md-12 main" style="height: 500px;border: 0.6px solid #E8E8E8;"></div>
</div>
<!--评论框-->
<div class="col-md-12">
    <h3>评论:</h3>
    <textarea name="comment" id="comment" cols="180" rows="10" placeholder="您的评论或留言"></textarea>
    <button  class="btn btn-info" type="button" name="comment-submit" id="comment-submit" tabindex="4">评论</button>
</div>
<!--评论列表-->
<div class="col-md-12 comment">
  <p>
      <span>名字</span>
      <span>(时间)</span>
  </p>
    <text>文字</text>
</div>
