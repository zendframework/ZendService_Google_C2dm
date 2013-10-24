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

/**
 * @group      ZendService
 * @group      ZendService_Google
 * @group      ZendService_Google_C2dm
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    protected $validData = array('key' => 'value', 'key2' => array('value'));

    public function setUp()
    {
        $this->m = new Message();
    }

    public function testExpectedRegistrationIdBehavior()
    {
        $this->assertEquals(null, $this->m->getRegistrationId());

        $this->m->setRegistrationId('0123456789');
        $this->assertEquals('0123456789', $this->m->getRegistrationId());

        $this->assertArrayHasKey('registration_id', $this->m->toPost());
    }

    public function testInvalidRegistrationIdThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->m->setRegistrationId(array('1234'));
    }

    public function testExpectedCollapseKeyBehavior()
    {
        $this->assertEquals($this->m->getCollapseKey(), null);
        $this->m->setCollapseKey('my collapse key');
        $this->assertEquals($this->m->getCollapseKey(), 'my collapse key');
        $this->assertArrayHasKey('collapse_key', $this->m->toPost());
    }

    public function testInvalidCollapseKeyThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->m->setCollapseKey(array('1234'));
    }

    public function testExpectedDataBehavior()
    {
        $this->assertEquals($this->m->getData(), array());
        $this->m->setData($this->validData);
        $this->assertEquals($this->m->getData(), $this->validData);

        foreach ($this->validData as $k => $v) {
            $this->assertArrayHasKey('data.' . $k, $this->m->toPost());
        }
        $this->m->clearData();
        $this->assertEquals($this->m->getData(), array());
        $this->m->addData('mykey', 'myvalue');
        $this->assertEquals($this->m->getData(), array('mykey' => 'myvalue'));
        $this->assertArrayHasKey('data.mykey', $this->m->toPost());
    }

    public function testInvalidDataThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->m->addData(array('1234'), 'value');
    }

    public function testExpectedDelayWhileIdleBehavior()
    {
        $this->assertEquals($this->m->getDelayWhileIdle(), false);
        $this->m->setDelayWhileIdle(true);
        $this->assertEquals($this->m->getDelayWhileIdle(), true);
        $this->assertArrayHasKey('delay_while_idle', $this->m->toPost());
        $this->m->setDelayWhileIdle(false);
        $this->assertEquals($this->m->getDelayWhileIdle(), false);
    }
}
