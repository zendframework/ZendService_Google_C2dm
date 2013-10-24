<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendServiceTest\Google\C2dm;

use ZendService\Google\C2dm\Message;
use ZendService\Google\C2dm\Response;

/**
 * @group      ZendService
 * @group      ZendService_Google
 * @group      ZendService_Google_C2dm
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->m = new Message();
    }

    public function testConstructorExpectedBehavior()
    {
        $response = new Response();
        $this->assertNull($response->getResponse());
        $this->assertNull($response->getMessage());

        $message = new Message();
        $response = new Response(null, $message);
        $this->assertEquals($message, $response->getMessage());
        $this->assertNull($response->getResult());

        $message = new Message();
        $responseString = 'id=1:1234';
        $response = new Response($responseString, $message);
        $this->assertEquals($responseString, $response->getResponse());
        $this->assertEquals($message, $response->getMessage());
    }

    public function testMessageExpectedBehavior()
    {
        $message = new Message();
        $response = new Response();
        $response->setMessage($message);
        $this->assertEquals($message, $response->getMessage());
    }

    public function testResponse()
    {
        $responseString = 'id=1:1234';
        $response = new Response();
        $response->setResponse($responseString);

        $this->assertEquals($responseString, $response->getResponse());
        $this->assertEquals('1:1234', $response->getId());
        $this->assertEquals(Response::RESULT_OK, $response->getResult());
        $this->assertFalse($response->isError());
    }
}
