<?php
require_once '../lib/ServiceLocator.php';
require_once '../lib/Exception.php';

use \wf\app\ServiceLocator;

class Inst {
    public function __construct($arg)
    {                
        print "\n\nInst->__construct\n----\n";
        print_r($arg);
        print "\n----\n";            
        return true; 
    }
    
    public function exec($arg)
    {
        print "\n\nInst->exec\n----\n";
        print_r($arg);
        print "\n----";            
        return true; 
    }
    
    public function test($arg) {
        return $arg;
    }
}

function slTest($arg) {
    return $arg;
}

/**
 * Config test case.
 */
class ConfigTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var ServiceLocator
     */
    private $locator;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        
        // TODO Auto-generated ConfigTest::setUp()
        
        $this->locator = new ServiceLocator();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated ConfigTest::tearDown()
        $this->locator = null;
        
        parent::tearDown();
    }
    
    /**
     * Constructs the test case.
     */
    public function __construct() {
        // TODO Auto-generated constructor
    }
    
    public function testAll()
    {
        // 类
        $this->locator->set('test_inst', "Inst", ["恭喜发财。。。"]);         
        $inst = $this->locator->get('test_inst');        
        $this->assertTrue($inst->exec(1111));
        
        // 对象
        $this->locator->set('test_obj', new Inst('测试对象!!!'), ['好，很好']);         
        $obj = $this->locator->get('test_obj');        
        $this->assertEquals(9999, $obj->test(9999));
        
        // 闭包
        $closure = function($arg) {
            print "---------\n";
            print_r($arg);
            print "\n---------";
            return $arg;
        };
        $this->locator->set('test_closure', $closure, ["yes!"]);
        $res = $this->locator->get('test_closure');
        $this->assertEquals('yes!', $res);
        
        // 函数
        $this->locator->set('test_fnc', 'slTest', ["yes or no"]);
        $res = $this->locator->get('test_fnc');
        $this->assertEquals('yes or no', $res);
        
        // 共享实例
        $this->locator->set('test_share', function() {
            return uniqid();
        }, [], true);
        $shareRes1 = $this->locator->get('test_share');
        $shareRes2 = $this->locator->get('test_share');
        $this->assertEquals($shareRes1, $shareRes2);

        // 不共享实例
        $this->locator->set('test_share', function() {
            return uniqid();
        }, [], false);
        $shareRes1 = $this->locator->get('test_share');
        $shareRes2 = $this->locator->get('test_share');
        $this->assertNotEquals($shareRes1, $shareRes2);
    }
    
}

