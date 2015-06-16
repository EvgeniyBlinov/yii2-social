<?php
namespace cent\yii2social\mappers;

use Yii;
use yii\base\Component;
use yii\log\Logger;

/**
 * Twitter
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Twitter
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
        $twitter              = Yii::$app->twitter->getTwitter();
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
        $hashtagsUrl          = implode('&', array_filter([
                                    implode(' OR ', $hashtags),
                                    http_build_query($options)
                                ]));
        $query                = "search/tweets.json?q=${hashtagsUrl}";
        if (defined('YII_DEBUG') && constant('YII_DEBUG')) {
            Yii::getLogger()->log("[Twitter API query] $query", Logger::LEVEL_WARNING);
        }
        $twitter              = $this->getClient();
        $twresult             = json_decode($twitter->get($query), true);
        return $this->getData($twresult['statuses']);
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
