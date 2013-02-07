<?php

require_once dirname(__FILE__) . '/../../rest/RestClient.php';
require_once dirname(__FILE__) . '/../../rest/Config.php';

/**
 * Test class for RestClient.
 */
class RestClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RestClient
     */
    private $object;
    private $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = new Config();
        $this->object = new RestClient($this->config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers RestClient::getUrl 
     * @covers RestClient::setUrl
     */
    public function testGetUrl_And_SetURl()
    {
        $url = 'http://test.de';
        $this->object->setUrl($url);
        $this->assertEquals($url, $this->object->getUrl());
    }

    /**
     * @covers RestClient::getHttpMethod 
     * @covers RestClient::setHttpMethod
     */
    public function testGetHttpMethod_And_SetHttpMethod()
    {
        $httpMethod = 'GET';
        $this->object->setHttpMethod($httpMethod);
        $this->assertEquals($httpMethod, $this->object->getHttpMethod());
    }

    /**
     * @covers RestClient::getDataToSend
     * @covers RestClient::setDataToSend
     */
    public function testGetDataToSend_And_SetDataToSend()
    {
        $dataToSend = "Test Data";
        $this->object->setDataToSend($dataToSend);
        $this->assertEquals("Test Data", $this->object->getDataToSend());

        $dataToSend = "Test Data";
        $contentMimeType = "text";
        $this->object->setDataToSend($dataToSend, $contentMimeType);
        $this->assertEquals("Test Data", $this->object->getDataToSend());
    }

    /**
     * @covers RestClient::sendCurlRequest
     */
    public function testSendCurlRequest()
    {
        $this->object->setHttpMethod('GET');
        $this->object->setUrl('http://dilbert.ub.uni-freiburg.de:8080/fedora');
        $result = $this->object->sendCurlRequest();
        $this->assertInstanceOf('RestResult', $result);
    }

    /**
     * @covers RestClient::sendCurlRequest
     */
    public function testSendCurlRequestWithSSL()
    {
        $this->config->checkServerCertificate = true;
        $this->config->serverCertificate = 'dilbert.ub.uni-freiburg.de.crt';
        $this->object->setHttpMethod('GET');
        $this->object->setUrl('https://dilbert.ub.uni-freiburg.de/fedora');
        $result = $this->object->sendCurlRequest();
        $this->assertInstanceOf('RestResult', $result);
    }

    /**
     * @covers RestClient::getHttpCodeTranslation
     */
    public function testGetHttpCodeTranslation()
    {
        $this->assertEquals('Bad Request',
            $this->object->getHttpCodeTranslation('400'));
        $this->assertEquals('Unauthorized',
            $this->object->getHttpCodeTranslation('401'));
    }

    /**
     * @covers RestClient::uploadFile
     */
    public function testUploadFile()
    {
        $postData = array('file' => '@../data/1.csv');
        $result = $this->object->uploadFile($postData);
        $this->assertEquals(1, preg_match("/uploaded:\/\/[0-9]*/", $result));
    }

    /**
     * @covers RestClient::uploadFile
     * @expectedException InvalidArgumentException
     */
    public function testUploadFileWithEmptyArray()
    {
        $postData = array();
        $this->assertTrue($this->object->uploadFile($postData));
    }

    /**
     * @covers RestClient::uploadFile
     * @expectedException InvalidArgumentException
     */
    public function testUploadFileWithMissingAt()
    {
        $postData = array('file' => 'data/1.csv');
        $this->assertTrue($this->object->uploadFile($postData));
    }

}

