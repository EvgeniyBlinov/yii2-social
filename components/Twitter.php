<?php
namespace cent\yii2social\components;

use Yii;
use yii\base\Component;

/**
 * Twitter component
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class Twitter extends Component
{
    /**
     * The Twitter Apps key, set in config
     * @var string
     */
    public $consumer_key = '';

    /**
     * The Twitter Apps secret key, set in config
     * @var string
     */
    public $consumer_secret    = '';

    /**
     * @var string of OAuth token
     **/
    public $oauth_token        = '';

    /**
     * @var string of OAuth token secret
     **/
    public $oauth_token_secret = '';

    /**
     * @var string of TwitterOAuth class name
     **/
    public $oAuthClass = '\richweber\twitter\TwitterOAuth';

    /**
     * @var mixed of client TwitterOAuth|NULL
     **/
    private $_client = null;

    /**
     * Use this one for when we need to authicate oursevles with twitter
     */
    public function init()
    {
        $this->_client = new $this->oAuthClass(
            $this->consumer_key,
            $this->consumer_secret,
            $this->oauth_token,
            $this->oauth_token_secret
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
