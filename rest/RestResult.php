<?php

/**
 * RestResult class file.
 * 
 * Container class that stores parameters from the rest response.
 *
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @author    Martin Helfer <martin.helfer@ub.uni-freiburg.de>
 * @copyright 2012 Freiburg University Library
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 */
class RestResult
{

    /**
     * the http method used for the request
     * @var String
     */
    private $_requestHttpMethod;

    /**
     * the data sent with the request
     * @var String 
     */
    private $_requestSentData;

    /**
     * the request URL
     * @var String 
     */
    private $_requestUrl;

    /**
     * the request port number
     * @var String 
     */
    private $_requestPort;

    /**
     * HTTP code returned
     * @var String 
     */
    private $_responseHttpCode;

    /**
     * MIME type returned
     * @var String
     */
    private $_responseMimeType;

    /**
     * the response content
     * @var String 
     */
    private $_responseContent;

    /**
     * the response error
     * @var String 
     */
    private $_responseError;

    /**
     * Get HTTP code returned
     * @return integer
     */
    public function getResponseHttpCode()
    {
        return $this->_responseHttpCode;
    }

    /**
     * Set HTTP code returned
     * @param string $responseHttpCode 
     */
    public function setResponseHttpCode($responseHttpCode)
    {
        $this->_responseHttpCode = $responseHttpCode;
    }

    /**
     * Get MIME type returned
     * @return string
     */
    public function getResponseMimeType()
    {
        return $this->_responseMimeType;
    }

    /**
     * Set MIME type returned
     * @param string $responseMimeType 
     */
    public function setResponseMimeType($responseMimeType)
    {
        $this->_responseMimeType = $responseMimeType;
    }

    /**
     * Get the response content
     * @return string
     */
    public function getResponseContent()
    {
        return $this->_responseContent;
    }

    /**
     * Set the response content
     * @param type $responseContent 
     */
    public function setResponseContent($responseContent)
    {
        $this->_responseContent = $responseContent;
    }

    /**
     * Get the response error
     * @return string
     */
    public function getResponseError()
    {
        return $this->_responseError;
    }

    /**
     * Set the response error
     * @param string $responseError 
     */
    public function setResponseError($responseError)
    {
        $this->_responseError = $responseError;
    }

    /**
     * @return boolean Indicates if this response object contains an error
     */
    public function hasError()
    {
        $error = $this->getResponseError();
        return isset($error) && strlen($error) > 0;
    }

    /**
     * Get the http method used for the request
     * @return string
     */
    public function getRequestHttpMethod()
    {
        return $this->_requestHttpMethod;
    }

    /**
     *  Set the http method used for the request
     * @param type $requestHttpMethod 
     */
    public function setRequestHttpMethod($requestHttpMethod)
    {
        $this->_requestHttpMethod = $requestHttpMethod;
    }

    /**
     * Get the data sent with the request
     * @return string
     */
    public function getRequestSentData()
    {
        return $this->_requestSentData;
    }

    /**
     * Set the data sent with the request
     * @param string $requestSentData 
     */
    public function setRequestSentData($requestSentData)
    {
        $this->_requestSentData = $requestSentData;
    }

    /**
     * Get the request URL
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->_requestUrl;
    }

    /**
     * Set the request URL
     * @param string $requestUrl 
     */
    public function setRequestUrl($requestUrl)
    {
        $this->_requestUrl = $requestUrl;
    }

    /**
     * Get the request port number
     * @return integer
     */
    public function getRequestPort()
    {
        return $this->_requestPort;
    }

    /**
     * Set the request port number
     * @param String $requestPort 
     */
    public function setRequestPort($requestPort)
    {
        $this->_requestPort = $requestPort;
    }

}
