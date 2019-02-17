<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 15:53
 */
namespace common\enterprise\service;

use common\enterprise\Entrance;
use common\models\enterprise\EnterpriseBoss;
use yii\helpers\Url;

class EnterpriseHandle extends Entrance{

    public $enterprise_id;

    protected $pattern;

    /**
     * @var
     * 前端token
     */
    protected $identity_token;

    const PATTERN_ORDINARY = "ordinary";

    const PATTERN_SEPARATE = "separate";

    public function __construct($enterprise_id , $pattern = ""){
        $this->enterprise_id = $enterprise_id;
        $enterprise = Entrance::instance($enterprise_id);
        $app = $enterprise->setAgentId("app");
        $this->access_token = $app->token->get();
        parent::__construct($this->access_token);
        $this->pattern = $pattern ? :self::PATTERN_SEPARATE;
        $this->identity_token = \Yii::$app->request->headers->get("identity_token");
    }

    /**
     * @param $enterprise_id
     * @return bool
     * 检测是否已经授权
     */
    public function isOauth($enterprise_id){
        if($this->pattern == self::PATTERN_ORDINARY){
            $session = \Yii::$app->session;
            if($session->get("enterprise_user_" . $enterprise_id)){
                return true;
            }
            return false;
        }elseif($this->pattern == self::PATTERN_SEPARATE){
            if($this->auditToken()){
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $identity_token
     * @return bool
     * 验证token是否有效
     */
    public function auditToken($identity_token = ""){
        $result = $this->analysisIdentityToken($identity_token);
        if($result){
            if(empty($result["user_id"]) || $result["exptime"] < time()){
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $identity_token
     * @return mixed
     * 解析身份token
     */
    public function analysisIdentityToken($identity_token = ""){
        $identity_token  = $identity_token  ? : $this->identity_token;
        if(empty($identity_token)) return false;
        return json_decode(base64_decode($identity_token) , true);
    }

    /**
     * @param $user_detail
     * @return string
     * 编译token
     */
    public function compileIdentityToken($user_detail){
        $user_detail["exptime"] = time() + 7200 * 30;
        return base64_encode(json_encode($user_detail));
    }

    /**
     * @param $enterprise_id
     * 检测授权
     */
    public function checkOauth(){
        $enterprise_id = $this->enterprise_id;
        if(!$this->isOauth($enterprise_id)){
            $config = self::getConfig($enterprise_id);
            if($this->pattern == self::PATTERN_ORDINARY){
                //嵌套模式
                $redirect_uri = \Yii::$app->request->getHostInfo().\Yii::$app->request->url;
                \Yii::$app->session->set("redirect_uri" , $redirect_uri);
                \Yii::$app->response->redirect($this->oauth->get_authorize_url($config["corp_id"] , $config["app"]["agent_id"],urlencode(Url::toRoute(["enterprise/callback" , "enterprise_id" => $this->enterprise_id],true))))->send();
                die;
            }elseif ($this->pattern == self::PATTERN_SEPARATE){
                //分离模式
                $redirect_uri = \Yii::$app->request->post("redirect_uri");
                return $get_authorize_url = $this->oauth->get_authorize_url($config["corp_id"] , $config["app"]["agent_id"],urlencode($redirect_uri));
            }
        }
        return true;
    }


}