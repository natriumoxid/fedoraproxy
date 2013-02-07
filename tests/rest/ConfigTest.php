<?php

require_once dirname(__FILE__) . '/../../rest/Config.php';

/**
 * Test class for SimpleConfig.
 * 
 * @author Franck Borel
 * @copyright (c) 2012 University Library of Freiburg
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = new Config;
    }

    /**
     * @covers Config::__set
     */
    public function test__setAnd__get()
    {
        $this->object->testValue = 'Test';
        $this->assertEquals('Test', $this->object->testValue);
    }

    /**
     * @covers Config::count
     */
    public function testCount()
    {
        $this->assertEquals(8, $this->object->count());
    }

    /**
     * @covers Config::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertTrue($this->object->offSetExists('login'));
        $this->assertFalse($this->object->offSetExists('test'));
    }

    /**
     * @covers Config::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals('fedoraAdmin', $this->object->offsetGet('login'));
    }

    /**
     * @covers Config::offsetSet
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet('login', 'admin');
        $this->assertEquals('admin', $this->object->offsetGet('login'));
    }

    /**
     * @covers Config::offsetUnset
     */
    public function testOffsetUnset()
    {
        $this->object->offsetUnset('maxresult');
        $this->assertFalse($this->object->offsetExists('maxresult'));
    }

    /**
     * @covers Config::getIterator
     */
    public function testGetIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->object->getIterator());
    }

}
