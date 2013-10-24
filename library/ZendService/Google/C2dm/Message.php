<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\Google\C2dm;

/**
 * Google Cloud 2 Device Messaging Message
 * This class defines a message to be sent
 * through the Google C2DM API.
 */
class Message
{

    /**
     * @var string
     */
    protected $registrationId;

    /**
     * @var string
     */
    protected $collapseKey;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $delayWhileIdle = false;

    /**
     * Set Registration Id
     *
     * @param string $id
     * @return Message
     */
    public function setRegistrationId($id)
    {
        if (!is_string($id) || empty($id)) {
            throw new Exception\InvalidArgumentException('$id must be a non-empty string');
        }
        $this->registrationId = $id;

        return $this;
    }

    /**
     * Get Registration Id
     *
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * Get Collapse Key
     *
     * @return string
     */
    public function getCollapseKey()
    {
        return $this->collapseKey;
    }

    /**
     * Set Collapse Key
     *
     * @param string $key
     * @return Message
     */
    public function setCollapseKey($key)
    {
        if (!is_null($key) && !(is_string($key) && strlen($key) > 0)) {
            throw new Exception\InvalidArgumentException('$key must be null or a non-empty string');
        }
        $this->collapseKey = $key;
        return $this;
    }

    /**
     * Set Data
     *
     * @param array $data
     * @return Message
     */
    public function setData(array $data)
    {
        $this->clearData();
        foreach ($data as $k => $v) {
            $this->addData($k, $v);
        }
        return $this;
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add Data
     *
     * @param string $key
     * @param mixed $value
     * @return Message
     */
    public function addData($key, $value)
    {
        if (!is_string($key) || empty($key)) {
            throw new Exception\InvalidArgumentException('$key must be a non-empty string');
        }
        if (in_array($key, $this->data)) {
            throw new Exception\RuntimeException('$key conflicts with current set data');
        }
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Clear Data
     *
     * @return Message
     */
    public function clearData()
    {
        $this->data = array();
        return $this;
    }

    /**
     * Set Delay While Idle
     *
     * @param bool $delay
     * @return Message
     */
    public function setDelayWhileIdle($delay)
    {
        $this->delayWhileIdle = (bool) $delay;
        return $this;
    }

    /**
     * Get Delay While Idle
     *
     * @return bool
     */
    public function getDelayWhileIdle()
    {
        return $this->delayWhileIdle;
    }

    /**
     * To Post
     * A utility method to return an array for posting
     * to the C2DM service.
     *
     * @return array
     */
    public function toPost()
    {
        $array = array(
            'registration_id'  => $this->getRegistrationId(),
            'collapse_key'     => $this->getCollapseKey(),
            'delay_while_idle' => (int) $this->getDelayWhileIdle(),
        );
        foreach ($this->getData() as $k => $v) {
            $k = 'data.' . $k;
            $array[$k] = $v;
        }

        return $array;
    }
}
