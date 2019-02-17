<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/25
 * Time: 23:59
 */
namespace common\helper;

use yii\base\Model;
use yii\helpers\Url;

class WeiXinWeb extends Model{

    public $txt;

    public function rules()
    {
        return [
            [['txt'], 'file', 'skipOnEmpty' => true, 'extensions' => 'txt','checkExtensionByMimeType' => false],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            if(!empty($this->txt)){
                $this->txt->saveAs(Url::to("@frontend/web/") . $this->txt->baseName . '.' . $this->txt->extension);
                return true;
            }
        } else {
            return false;
        }
    }


}