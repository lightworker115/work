<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/25
 * Time: 23:59
 */
namespace common\helper;

use yii\base\Model;

class Certificate extends Model{

    public $file;

    public $savePosition = '';

    static public $filename = ['apiclient_cert','apiclient_key'];

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pem ','checkExtensionByMimeType' => false, 'maxFiles' => 4],
        ];
    }

    public function upload(){
        File::detectionUploadPath($this->savePosition);
        if($this->validate()){
            foreach ($this->file as $f){
                if(in_array($f->baseName,self::$filename)){
                    $filename = $f->baseName .'.'.$f->extension;
                    $f->saveAs($this->savePosition . '/' . $filename);
                }
            }
            return true;
        }
        return false;
    }

    public function setSavePosition($business_id){
        $this->savePosition = dirname(dirname(__DIR__)).'/certificate/business_'.$business_id;
    }


}