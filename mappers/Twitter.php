<?php
namespace cent\yii2social\mappers;

use Yii;
use yii\base\Component;
use yii\log\Logger;

use cent\yii2social\mappers\AbstractMapper;

/**
 * Twitter
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Twitter extends AbstractMapper
{
    /**
     * Get social name
     *
     * @return string
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getSocialName()
    {
        return 'twitter';
    }

    /**
     * Get client
     *
     * @return object
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getClient()
    {
        $twitter              = Yii::$app->twitter->init();
        $twitter->decode_json = false;
        return $twitter;
    }

    /**
     * Get by hashtags
     *
     * @param mixed $hashtags
     * @param array $options
     * @return array
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getByHashtags($hashtags, array $options = [])
    {
        $hashtags             = array_map(function ($hashtag){ return urlencode('#' . $hashtag); }, (array) $hashtags);
        $urlOptions           = !empty($options['urlOptions']) ? $options['urlOptions'] : [];
        $hashtagsUrl          = implode('&', array_filter([
                                    implode(' OR ', $hashtags),
                                    http_build_query($urlOptions)
                                ]));
        $query                = "search/tweets.json?q=${hashtagsUrl}";
        if (defined('YII_DEBUG') && constant('YII_DEBUG')) {
            Yii::getLogger()->log("[Twitter API query] $query", Logger::LEVEL_WARNING);
        }
        $twitter              = $this->getClient();
        $twresult             = json_decode($twitter->get($query), true);
        return $this->getData($twresult['statuses']);
    }
}
