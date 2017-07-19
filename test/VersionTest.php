<?php
require_once __DIR__ . '/../lib/Version.php';

use wf\app\Version;

/**
 * Version test case.
 */
class VersionTest extends PHPUnit_Framework_TestCase
{


    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Tests Version::getComponentsVersion()
     */
    public function testGetLatest()
    {
        $latest = Version::getLatest();
        $this->assertNotEmpty($latest);
    }

}

