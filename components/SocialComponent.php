<?php
namespace cent\yii2social\components;

use Yii;
use yii\base\Component;

/**
 * Social component
 *
 * @package yii2-social
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
**/
class SocialComponent extends Component
{
    /**
     * Social resources
     * @var array
     */
    public $resources = [];

    /**
     * @var array of options
     **/
    public $options    = [];

    /**
     * @var array of social elements
     **/
    private $_data    = [];

    /**
     * @var array of mapped resources
     **/
    private $_mappedResources = [];

    /**
     * Initializes the object
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration
     */
    public function init()
    {
        $this->checkResources();
        $this->applyResources();
    }

    /**
     * Check resources
     *
     * @return void
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function checkResources()
    {
        foreach ($this->resources as $resourceName => $resourceOptions) {
            if (!is_string($resourceName) || !class_exists($resourceName)) {
                throw new \Exception("Class $resourceName does not exists!");
            }
            $availableResourceOptions = [
                'model' => [
                    'required' => true,
                    'type'     => 'class',
                ],
            ];
            foreach ($availableResourceOptions as $optionName => $optionValue) {
                if ($optionValue['required']) {
                    if (empty($resourceOptions[$optionName])) {
                        throw new \Exception("Resource $resourceName has invalid option $optionName");
                    }
                    // code...
                }
            }
            // code...
        }
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
        foreach ($this->resources as $resource) {
            $this->setData(
                $resource->getByHashtags($hashtags, $options)
            );
        }
        if (!empty($this->options['sort'])) {
            $this->sortData('created_at', 'ASC', true);
        }
        return $this->getData();
    }

    /**
     * Set data
     *
     * @param array $data
     * @return SocialComponent
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function setData(array $data)
    {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }

    /**
     * Get data
     *
     * @param boolean $filterMetadata
     * @return array
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function getData($filterMetadata = true)
    {
        if ($filterMetadata) {
            return array_map(function ($element){
                unset($element['_social_name']);
                return $element;
            }, $this->_data);
        }
        return $this->_data;
    }

    /**
     * Sort data
     *
     * @param string $attribute
     * @param string $order
     * @param boolean $save
     * @return array of SocialComponent::$_data
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function sortData($attribute, $order = 'ASC', $save = true)
    {
        $data = $this->_data;
        $self = $this;
        usort($data, function ($a, $b) use ($attribute, $order) {
            if ($a[$attribute] == $b[$attribute]) {
                return 0;
            }
            return ($a[$attribute] < $b[$attribute]) ? -1 : 1;
        });
        if ($save) {
            $this->_data = $data;
        }
        return $data;
    }

    /**
     * Apply resources
     *
     * @return SocialComponent
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    public function applyResources()
    {
        foreach ($this->resources as $resourceName => $resourceOptions) {
            $resourceObject                 = new $resourceName();
            $resourceObject->model          = $resourceOptions['model'];
            $this->resources[$resourceName] = $resourceObject;
            $this->_mappedResources[$resourceObject->getSocialName()] = $resourceObject;
        }
        return $this;
    }

    /**
     * Get mapped resource by social name
     *
     * @param string $socialName
     * @return void
     * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
     **/
    //public function getMRBySN($socialName)
    //{
        //return (!empty($this->_mappedResources[$socialName])) 
            //? $this->_mappedResources[$socialName]
            //: null;
    //}

    //public function setResources(array $resources)
    //{
        //foreach ($resources as $resource) {
            //switch (gettype($resource)) {
                //case 'string':
                    //$this->_resources[] = $resource;
                    //break;
                //case 'object':
                    //if ($resource instanceof \yii\base\Object) {
                        //$resource = $resource::className();
                    //}
                    //$this->_resources[] = get_class($resource);
                    //break;
                
                //default:
                    //// code...
                    //break;
            //}
            //if (gettype($resource) == '') {
                //// code...
            //}
        //}
    //}

    //public function setOptions(array $options)
    //{
        //// array merge options
    //}

    //public function getOptions()
    //{
        //return $this->_options;
    //}
}
