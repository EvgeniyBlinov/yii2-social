<?php
namespace cent\yii2social\interfaces;

/**
 * AbstractSocialModel
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
interface ISocialModel
{
    /**
     * Get mapped fields
     *
     * @return array socialname => [ $socialField => $modelField ]
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getMappedFields();
}
