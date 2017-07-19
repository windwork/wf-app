<?php
require_once '../lib/Config.php';

use \wf\app\Config;

/**
 * Config test case.
 */
class ConfigTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var Config
     */
    private $config;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        
        // TODO Auto-generated ConfigTest::setUp()
        
        $this->config = new Config('./config', 'develop');
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated ConfigTest::tearDown()
        $this->config = null;
        
        parent::tearDown();
    }
    
    /**
     * Constructs the test case.
     */
    public function __construct() {
        // TODO Auto-generated constructor
    }
    
    /**
     * Tests Config->set()
     */
    public function testAll() {
        $this->assertEmpty($this->config->getAll());        
        $this->config->load('test');        
        $this->assertNotEmpty($this->config->getAll());

        // 修改一个选项
        $chOne = '中文水电费';
        $this->config->set('chOne', $chOne);
        $getStr = $this->config->get('chOne');        
        $this->assertEquals($getStr, $chOne);
        
        // 修改多个选项
        $chMulA = [
            'arr' => [
                'key1' => 2345678,
            ],
            'db' => [
                'master' => [
                    'host' => 'master_db',
                    'port' => 9999,
                ],
            ]
        ];
        $chMulB = [
            'db' => [
                'slave' => [
                    'pass' => '0000xxxx'
                ],
            ]
        ];
        $this->config->merge($chMulA);
        $this->config->merge($chMulB);
        
        $arrKey1 = $this->config->get('arr.key1');
        
        $this->assertEquals($arrKey1, $chMulA['arr']['key1']);
        $this->assertEquals($this->config->get('db.master.port'), $chMulA['db']['master']['port']);
        $this->assertEquals($this->config->get('db.slave.pass'), $chMulB['db']['slave']['pass']);

        // 删除
        $this->config->set('arr.key1', null);
        $this->assertNull($this->config->get('arr.key1'));     
    }
    
    public function testSet() {
        $conf = new Config('./config', 'develop');
        $checked = false;
        
        // 非正式环境set检测下标不能有.
        $checked = false;
        try {
            $conf->set('a.b', ['xx.yy' => 45678]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $checked = true;
        }
        $this->assertTrue($checked);
        
        // 正式环境不检测下标中是否带.号（从性能考虑）
        $conf = new Config('./config', 'product');
        $checked = false;
        try {
            $conf->set('a.b', ['xx.yy' => 45678]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $checked = true;
        }
        $this->assertFalse($checked);
    }
}

