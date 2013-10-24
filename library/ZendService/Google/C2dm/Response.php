<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\Google\C2dm;

/**
 * Google Cloud 2 Device Messaging Response
 * This class parses out the response from
 * the Google C2DM API
 */
class Response
{
    const RESULT_OK                    = 'ok';
    const RESULT_QUOTA_EXCEEDED        = 'QuotaExceeded';
    const RESULT_DEVICE_QUOTA_EXCEEDED = 'DeviceQuotaExceeded';
    const RESULT_INVALID_REGISTRATION  = 'InvalidRegistration';
    const RESULT_NOT_REGISTERED        = 'NotRegistered';
    const RESULT_MESSAGE_TOO_BIG       = 'MessageTooBig';
    const RESULT_MISSING_COLLAPSE_KEY  = 'MissingCollapseKey';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $result;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var array
     */
    protected $response;

    /**
     * Constructor
     *
     * @param string $response
     * @param Message $message
     * @return self
     */
    public function __construct($response = null, Message $message = null)
    {
        if ($response) {
            $this->setResponse($response);
        }

        if ($message) {
            $this->setMessage($message);
        }
    }

    /**
     * Get Message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Message
     *
     * @param Message $message
     * @return self
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get Response
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set Response
     *
     * @param string $response
     * @return self
     */
    public function setResponse($response)
    {
        $this->response = $response;
        $response = preg_split('/=/', $response);
        if (!isset($response[0]) || !isset($response[1])) {
            throw new Exception\RuntimeException('Invalid Response');
        }

        if (strtolower($response[0]) == 'error') {
            $this->id     = null;
            $this->result = $response[1];
        } else {
            $this->id = $response[1];
            $this->result = self::RESULT_OK;
        }

        return $this;
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Is Error
     *
     * @return bool
     */
    public function isError()
    {
        return (self::RESULT_OK != $this->getResult());
    }

    /**
     * Get Result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }
}
