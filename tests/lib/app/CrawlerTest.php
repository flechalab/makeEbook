<?php

namespace MakeEbook;

require_once dirname(__FILE__) . '/../../../lib/app/Crawler.class.php';

/**
 * Test class for Crawler.
 * Generated by PHPUnit on 2011-05-19 at 16:14:54.
 */
class CrawlerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Crawler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Crawler;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     * testSetUrls()
     */
    public function testSetUrls() {
        try {
            $this->object->setUrls('http://uol.com.br');
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
    }

    /**
     * testGetUrls()
     */
    public function testGetUrls() {
        try {
            $result = $this->object->getUrls();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
    }

    /**
     * testSettings()
     */
    public function testSettings() {
        try {
            $this->object->settings();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
    }

    /**
     * testSetFile()
     */
    public function testSetFile() {
        // method disabled 
    }

    /**
     * testSetString().
     */
    public function testSetString() {
        try {
            $this->object->setString();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
    }

    /**
     * testExec().
     */
    public function testExec() {
        $this->object->exec();
    }

    /**
     * testGetResult().
     */
    public function testGetResult() {
        try {
            $result = $this->object->getUrls();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
    }

    /**
     * testInfo().
     */
    public function testInfo() {
        try {
            $result = $this->object->info();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $result);
    }

    /**
     * testInfo_status().
     */
    public function testInfo_status() {
        try {
            $result = $this->object->info_status();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
        $this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $result);
    }

    /**
     * testClose().
     */
    public function testClose() {
        try {
            $result = $this->object->close();
        } 
        catch (Exception $e) {
            $this->fail('Error: ' . __FUNCTION__ . chr(10) . $e->getMessage());
        }
    }

}

?>
