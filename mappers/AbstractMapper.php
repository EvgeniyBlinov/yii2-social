<?php
namespace cent\yii2social\mappers;

/**
 * AbstractMapper
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
abstract class AbstractMapper
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
    abstract public function getSocialName();

    /**
     * Get client
     *
     * @return object
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    abstract public function getClient();

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
                    $socialData        = self::getSubElement($element, $socialField, null);
                    $data[$modelField] = (is_scalar($socialData) || $socialData == null) ?  $socialData : json_encode($socialData);
                }
                $data['_social_name'] = $element['_social_name'];
                return $data;
            },
            $rawData
        );
    }

    /**
     * Get sub element
     *
     * @param array $array
     * @param string $path
     * @param mixed $default
     * @return mixed
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public static function getSubElement(array $array, $path, $default = null)
    {
        if (!empty($path) && is_string($path)) {
            foreach (explode('.', $path) as $key) {
                $type = explode(":", $key);
                $key  = $type[0];
                if (isset($array[$key])) {
                    if (!empty($type[1])) {
                        settype($array[$key], $type[1]);
                    }
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }

        return $array; 
    }
}
