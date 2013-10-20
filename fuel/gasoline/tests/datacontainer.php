<?php namespace Gasolne;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

/**
 * @group   Gasoline
 */
class Test_DataContainer extends \Fuel\Core\TestCase {
    
    public function testSetGetData()
    {
        $c = new \Gasoline\DataContainer();
        $data = array('foo' => 'bar');
        
        // Test for an empty container
        $this->assertEquals(array(), $c->get_data());
        
        // Set the whole data and test it worked
        $c->set_data($data);
        $this->assertEquals($data, $c->get_data());
        
        // Set and test only a certain key
        $c->set('foo', 'bar');
        $this->assertEquals('bar', $c->get('foo'));
        
        // Get a non-existing key
        $this->assertEquals('no-value', $c->get('no-key', 'no-value'));
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testDeleteData()
    {
        $c = new \Gasoline\DataContainer();
        $c->foo = 'bar';
        
        $this->assertTrue($c->delete('foo'));
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testCountData()
    {
        $c = new \Gasoline\DataContainer();
        $data = array(
            'foo' => 'bar',
            'fooz' => 'baz'
        );
        
        $c->set_data($data);
        
        $this->assertEquals(count($data), count($c));
        
        $this->assertEquals(count($data), count($c));
    }
    
    
    /**
     * @depends testSetGetData
     * @depends testCountData
     */
    public function testClearData()
    {
        $c = new \Gasoline\DataContainer();
        $c->foo = 'bar';
        $c->set('fooz', 'baz');
        
        $this->assertEquals(2, count($c));
        
        $c->delete_data();
        
        $this->assertEquals(0, count($c));
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testHasData()
    {
        $c = new \Gasoline\DataContainer();
        $c->foo = 'bar';
        
        $this->assertTrue($c->has('foo'));
        $this->assertFalse($c->has('bar'));
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testSetReadonly()
    {
        $c = new \Gasoline\DataContainer();
        
        $c->readonly(true);
        
        $this->assertTrue($c->is_readonly());
    }
    
    
    /**
     * @depends testSetGetData
     * @depends testSetReadonly
     * @expectedException RuntimeException
     */
    public function testReadonlySetData()
    {
        $c = new \Gasoline\DataContainer();
        $c->readonly(true);
        
        $c->foo = 'bar';
    }
    
    
    /**
     * @depends testSetGetData
     * @depends testSetReadonly
     * @expectedException RuntimeException
     */
    public function testReadonlyDeleteData()
    {
        $c = new \Gasoline\DataContainer();
        $c->foo = 'bar';
        
        $c->readonly(true);
        
        $c->delete('foo');
    }
    
    
    /**
     * @depends testSetGetData
     * @depends testSetReadonly
     * @expectedException RuntimeException
     */
    public function testReadonlyClearData()
    {
        $c = new \Gasoline\DataContainer();
        $c->foo = 'bar';
        
        $c->readonly(true);
        
        $c->delete_data();
    }
    
    
    /**
     * @depends testSetGetData
     * @expectedException OutOfBoundsException
     */
    public function testArrayAccessSetGetData()
    {
        $c = new \Gasoline\DataContainer();
        $data = array('foo' => 'bar', 'fooz' => 'baz');
        
        // Test array access parts
        $c['foo'] = 'bar';
        $this->assertEquals('bar', $c['foo']);
        
        // Access an in-accessible property, shoudl throw an OutOfBoundsException
        $c['exception'];
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testArrayAccessDeleteData()
    {
        $c = new \Gasoline\DataContainer();
        $c['foo'] = 'bar';
        
        unset($c['foo']);
    }
    
    
    /**
     * @depends testSetGetData
     */
    public function testArrayAccessHasData()
    {
        $c = new \Gasoline\DataContainer();
        $c['foo'] = 'bar';
        
        $this->assertTrue(isset($c['foo']));
    }
    
}
