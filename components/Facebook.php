<?php
namespace cent\yii2social\components;

use Yii;
use yii\base\Component;

/**
 * Facebook component
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Facebook extends Component
{
    /**
     * The Facebook Apps id, set in config
     * @var string
     */
    public $appId = '';

    /**
     * The Facebook Apps secret key, set in config
     * @var string
     */
    public $appSecret    = '';

    /**
     * @var string of OAuth token
     **/
    public $oAToken        = '';

    /**
     * @var string of FacebookSession class name
     **/
    public $oAuthClass = 'Facebook\FacebookSession';

    /**
     * @var mixed of client TwitterOAuth|NULL
     **/
    private $_client = null;

    /**
     * Use this one for when we need to authicate oursevles with twitter
     */
    public function init()
    {
        $oAuthClass = $this->oAuthClass;
        $oAuthClass::setDefaultApplication($this->appId, $this->appSecret);
        $this->_client = new $oAuthClass(
            $this->oAToken
        );
        return $this->_client;
    }

    /**
     * Call client function
     *
     * @return mixed
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function __call($name, $args)
    {
        if (!method_exists($this, $name)) {
            return call_user_func_array([$this->_client, $name], $args);
        }
        return call_user_func_array([$this, $name], $args);
    }

    /**
     * Get client property
     *
     * @return mixed
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            return $this->_client->$name;
        }

        return $this->$name;
    }
}
