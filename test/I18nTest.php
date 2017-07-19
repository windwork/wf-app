<?php
require_once '../lib/I18n.php';

use wf\app\I18n;

/**
 * I18n test case.
 */
class I18nTest extends PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @var I18n
     */
    private $i18n;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated I18nTest::setUp()
        
        $this->i18n = new I18n(/* parameters */);
        $this->i18n->setDir(__DIR__.'/i18n\\/')->setLocale('zh_CN');        
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated I18nTest::tearDown()
        $this->i18n = null;
        
        parent::tearDown();
    }
    
    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }
        
    /**
     * Tests I18n->getLocale()
     */
    public function testGetLocale()
    {
        $locale = $this->i18n->getLocale();
        $this->assertEquals('zh_CN', $locale);
        
        $this->i18n->setLocale('en_US');
        $locale = $this->i18n->getLocale();
        $this->assertEquals('en_US', $locale);
    }
    
    /**
     * Tests I18n->get()
     */
    public function testGet()
    {        
        $this->assertEmpty($this->i18n->getLangs());        
        $hi = $this->i18n->get('hi');
        $this->assertEquals('hi', $hi); // 未设置本地化项返回下标
        
        $this->i18n->setLocale('en_US');
        $this->i18n->addLang('test');
        $hi = $this->i18n->get('hi');
        $this->assertEquals('hello', $hi);
        $this->assertNotEmpty($this->i18n->getLangs());

        $this->i18n->setLocale('zh_CN');
        $this->i18n->addLang('test');
        $hi = $this->i18n->get('hi');
        $this->assertEquals('你好', $hi);
        $this->assertNotEmpty($this->i18n->getLangs());
    }
        
    /**
     * Tests I18n->getDir()
     */
    public function testGetDir()
    {
        $dir = $this->i18n->getDir();
        $this->assertEquals(__DIR__ . '/i18n', $dir);
    }
}

