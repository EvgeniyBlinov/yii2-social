<?php
namespace cent\yii2social\mappers;

use Yii;
use yii\base\Component;
use yii\log\Logger;

use Facebook\Entities\AccessToken;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

/**
 * Facebook
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Facebook
{
    /**
     * @var string
     **/
    public $model;

    /**
     * Get social name
     *
     * @return string
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getSocialName()
    {
        return 'facebook';
    }

    /**
     * Get client
     *
     * @return object
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getClient()
    {
        return Yii::$app->facebook->init();
    }

    public function getFeed(array $options = [])
    {
        $data = [];
        if ($session = $this->getClient()) {
            //$session = new FacebookSession(FacebookSession::newAppSession()->getAccessToken());
            $session = new FacebookSession(APP_SOCIAL_FB_OATOKEN);
            //$session = FacebookSession::newAppSession();
            //echo "<pre>";var_dump(

                //$session->getAccessToken()
                
                ////,
                ////(new AccessToken($session->getAccessToken()))
                //->extend()
            //);die;
            try {
                $response = (new FacebookRequest(
                    //$session, 'GET', '/me/posts'
                    $session, 'GET', '/me/accounts?fields=name,access_token,perms'
                    //$session, 'GET', '/me'
                    //, array(
                            //'link' => 'www.example.com',
                            //'message' => 'User provided message'
                        //)
                    ))->execute()->getGraphObject()->asArray();
                echo "<pre>";var_dump(
                $response
                );die;
                //echo "Posted with id: " . $response->getProperty('id');
            } catch(\Exception $e) {
                echo "Exception occured, code: " . $e->getCode();
                echo " with message: " . $e->getMessage();
            }
        }
        return $this->getData($data);
    }

    /**
     * Get data
     *
     * @param array $rawData
     * @return array
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getData(array $rawData)
    {
        $socialName   = $this->getSocialName();
        $model        = new $this->model;
        $mappedFields = $model->getMappedFields();
        $mappedFields = !empty($mappedFields[$socialName]) ? $mappedFields[$socialName] : [];
        return array_map(
            function ($element) use ($socialName, $mappedFields) {
                $data                    = [];
                $element['_social_name'] = $socialName;
                foreach ($mappedFields as $socialField => $modelField) {
                    $socialData        = $element[$socialField];
                    $data[$modelField] = is_scalar($socialData) ? (string) $socialData : json_encode($socialData);
                }
                $data['_social_name'] = $element['_social_name'];
                return $data;
            },
            $rawData
        );
    }
}
