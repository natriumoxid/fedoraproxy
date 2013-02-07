<?php

require_once dirname(__FILE__) . '/../../rest/RestResult.php';

/**
 * Test class for RestResult.
 */
class RestResultTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RestResult
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RestResult;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object = null;
    }

    /**
     * @covers RestResult::setResponseHttpCode
     * @covers RestResult::getResponseHttpCode
     */
    public function testSetResponseHttpCode_And_GetResponseHttpCode()
    {
        $responseHttpCode = '200';
        $this->object->setResponseHttpCode($responseHttpCode);
        $this->assertEquals($responseHttpCode,
            $this->object->getResponseHttpCode());
    }

    /**
     * @covers RestResult::setResponseMimeType
     * @covers RestResult::getResponseMimeType
     */
    public function testSetResponseMimeType_And_GetResponseMimeType()
    {
        $responseMimeType = 'text';
        $this->object->setResponseMimeType($responseMimeType);
        $this->assertEquals($responseMimeType,
            $this->object->getResponseMimeType());
    }

    /**
     * @covers RestResult::setResponseContent
     * @covers RestResult::getResponseContent
     */
    public function testSetResponseContent_And_GetResponseContent()
    {
        $responseContent = 'text';
        $this->object->setResponseContent($responseContent);
        $this->assertEquals($responseContent,
            $this->object->getResponseContent());
    }

    /**
     * @covers RestResult::setResponseError
     * @covers RestResult::getResponseError
     * 
     */
    public function testSetResponseError_And_GetResponseError()
    {
        $responseError = 'text';
        $this->object->setResponseError($responseError);
        $this->assertEquals($responseError, $this->object->getResponseError());
    }

    /**
     * @covers RestResult::hasError
     */
    public function testHasError()
    {
        $responseError = 'Error';
        $this->object->setResponseError($responseError);
        $this->assertTrue($this->object->hasError());

        $responseError = '';
        $this->object->setResponseError($responseError);
        $this->assertFalse($this->object->hasError());
    }

    /**
     * @covers RestResult::setRequestHttpMethod
     * @covers RestResult::getRequestHttpMethod
     */
    public function testGetRequestHttpMethod()
    {
        $requestHttpMethod = 'GET';
        $this->object->setRequestHttpMethod($requestHttpMethod);
        $this->assertEquals($requestHttpMethod,
            $this->object->getRequestHttpMethod());
    }

    /**
     * @covers RestResult::getRequestSentData
     * @covers RestResult::setRequestSentData
     */
    public function testGetRequestSentData()
    {
        $requestSentData = 'Data';
        $this->object->setRequestSentData($requestSentData);
        $this->assertEquals($requestSentData,
            $this->object->getRequestSentData());
    }

    /**
     * @covers RestResult::getRequestUrl
     * @covers RestResult::setRequestUrl
     */
    public function testGetRequestUrl()
    {
        $requestRequestUrl = 'http://test.url.com';
        $this->object->setRequestUrl($requestRequestUrl);
        $this->assertEquals($requestRequestUrl, $this->object->getRequestUrl());
    }

    /**
     * @covers RestResult::getRequestPort
     * @covers RestResult::setRequestPorts
     */
    public function testGetRequestPort()
    {
        $requestPort = '8080';
        $this->object->setRequestPort($requestPort);
        $this->assertEquals($requestPort, $this->object->getRequestPort());
    }

}
