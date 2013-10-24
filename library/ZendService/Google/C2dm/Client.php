<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\Google\C2dm;

use Zend\Http\Client as HttpClient;

/**
 * Cloud 2 Device Messaging Client
 * This class allows the ability to send out messages
 * through the Google C2DM API.
 */
class Client
{
    /**
     * @const string Server URI
     */
    const SERVER_URI = 'https://android.apis.google.com/c2dm/send';

    /**
     * @var Zend\Http\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $token;

    /**
     * Get API Token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set API Token
     *
     * @param string $token
     * @return Client
     */
    public function setToken($token)
    {
        if (!is_string($token) || empty($token)) {
            throw new Exception\InvalidArgumentException('The api key must be a string and not empty');
        }
        $this->token = $token;
        return $this;
    }

    /**
     * Get HTTP Client
     *
     * @return Zend\Http\Client
     */
    public function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = new HttpClient();
            $this->httpClient->setOptions(array('strictredirects' => true));
        }
        return $this->httpClient;
    }

    /**
     * Set HTTP Client
     *
     * @param Zend\Http\Client
     * @return Client
     */
    public function setHttpClient(HttpClient $http)
    {
        $this->httpClient = $http;
        return $this;
    }

    /**
     * Send Message
     *
     * @param Mesage $message
     * @return Response
     */
    public function send(Message $message)
    {
        $client = $this->getHttpClient();
        $client->setUri(self::SERVER_URI);
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaderLine('Authorization', 'GoogleLogin auth=' . $this->getToken());

        $response = $client->setHeaders($headers)
                           ->setMethod('POST')
                           ->setParameterPost($message->toPost())
                           ->send();

        switch ($response->getStatusCode()) {
            case 503:
                $exceptionMessage = '503 Server Unavailable';
                if ($retry = $response->getHeaders()->get('Retry-After')) {
                    $exceptionMessage .= '; Retry After: ' . $retry;
                }
                throw new Exception\RuntimeException($exceptionMessage);
                break;
            case 401:
                throw new Exception\RuntimeException('401 Forbidden; Authentication Error');
                break;
        }

        return new Response($response->getBody(), $message);
    }
}
