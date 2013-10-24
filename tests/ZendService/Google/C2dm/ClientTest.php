<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\Google\C2dm;

use Zend\Http\Client\Adapter\Test;
use Zend\Http\Client as HttpClient;
use ZendService\Google\C2dm\Client;
use ZendService\Google\C2dm\Message;
use ZendService\Google\C2dm\Response;

/**
 * @group      ZendService
 * @group      ZendService_Google
 * @group      ZendService_Google_C2dm
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $httpAdapter;
    protected $httpClient;
    protected $client;
    protected $message;

    public function setUp()
    {
        $this->httpClient = new HttpClient();
        $this->httpAdapter = new Test();
        $this->httpClient->setAdapter($this->httpAdapter);
        $this->client = new Client();
        $this->client->setHttpClient($this->httpClient);
        $this->client->setToken('testing');
        $this->message = new Message();
        $this->message->setRegistrationId('foo');
        $this->message->setCollapseKey('bar');
        $this->message->addData('testKey', 'testValue');
    }

    public function testSetTokenThrowsExceptionOnNonString()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->setToken(array());
    }

    public function testSetToken()
    {
        $key = 'a-login-token';
        $this->client->setToken($key);
        $this->assertEquals($key, $this->client->getToken());
    }

    public function testGetHttpClientReturnsDefault()
    {
        $client = new Client();
        $this->assertEquals('Zend\Http\Client', get_class($client->getHttpClient()));
        $this->assertTrue($client->getHttpClient() instanceof HttpClient);
    }

    public function testSetHttpClient()
    {
        $client = new HttpClient();
        $this->client->setHttpClient($client);
        $this->assertEquals($client, $this->client->getHttpClient());
    }

    public function testSendThrowsExceptionWhenInvalidAuthToken()
    {
        $this->setExpectedException('RuntimeException');
        $this->httpAdapter->setResponse('HTTP/1.1 401 Unauthorized' . "\r\n\r\n");
        $this->client->send($this->message);
    }

    public function testSendThrowsExceptionWhenServiceUnavailable()
    {
        $this->setExpectedException('RuntimeException');
        $this->httpAdapter->setResponse('HTTP/1.1 503 Service Unavailable' . "\r\n\r\n");
        $this->client->send($this->message);
    }

    public function testSendResultQuotaExceeded()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=QuotaExceeded'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_QUOTA_EXCEEDED, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultDeviceQuotaExceeded()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=DeviceQuotaExceeded'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_DEVICE_QUOTA_EXCEEDED, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultInvalidRegistrationId()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=InvalidRegistration'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_INVALID_REGISTRATION, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultNotRegistered()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=NotRegistered'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_NOT_REGISTERED, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultMessageTooBig()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=MessageTooBig'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_MESSAGE_TOO_BIG, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultMissingCollapseKey()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'error=MissingCollapseKey'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_MISSING_COLLAPSE_KEY, $response->getResult());
        $this->assertTrue($response->isError());
    }

    public function testSendResultOk()
    {
        $this->httpAdapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'id=1234567890'
        );
        $response = $this->client->send($this->message);

        $this->assertEquals(Response::RESULT_OK, $response->getResult());
        $this->assertFalse($response->isError());
        $this->assertEquals("1234567890", $response->getId());
    }
}
