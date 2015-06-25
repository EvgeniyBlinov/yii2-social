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

use cent\yii2social\mappers\AbstractMapper;

/**
 * Facebook
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Facebook extends AbstractMapper
{
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

    /**
     * Get feed
     *
     * @param string $url
     * @param array $options
     * @return array
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function get($url, array $options = [])
    {
        $data = [];
        if ($session = $this->getClient()) {
            try {
                $response = (new FacebookRequest(
                    $session,
                    'GET',
                    $url,
                    !empty($options['urlOptions']) ? $options['urlOptions'] : []
                ))->execute()->getGraphObject()->asArray();
                if (!empty($options['preFormatter'])) {
                    $formatter = $options['preFormatter'];
                    if (is_callable($formatter)) {
                        $response = call_user_func_array($formatter, $response);
                    }
                }
                $data = $response['data'];
            } catch(\Exception $e) {
                //echo "Exception occured, code: " . $e->getCode();
                //echo " with message: " . $e->getMessage();
            }
        }

        return $this->getData($data);
    }
}
