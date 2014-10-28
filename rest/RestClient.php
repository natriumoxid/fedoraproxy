<?php

require_once dirname(__FILE__) . '/RestResult.php';
require_once('Log.php');

/**
 * RestClient class file.
 * 
 * This class send REST requests to a REST service.
 * The request will be sent by using the curl library if the mod_curl module is 
 * installed.
 * 
 * @package   FedoraProxy
 * @author    Laurent Opprecht <laurent.opprecht@unige.ch>
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @copyright 2012 Freiburg University Library
 * @copyright 2010 University of Geneva
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 */
class RestClient
{

    const REQUEST = 'request';
    const RESPONSE = 'response';
    const RESPONSE_TARGET_URL = 'RESULT_TARGET_URL';
    const RESPONSE_ERROR = 'RESULT_ERROR';
    const RESPONSE_MIME = 'RESULT_MIME';
    const RESPONSE_CONTENT = 'RESULT_CONTENT';
    const RESPONSE_HTTP_CODE = 'RESULT_HTTP_CODE';

    /**
     * Config instance
     * @var Object
     */
    private $_config;

    /**
     * The URL of the REST service
     * @var String 
     */
    private $_url = null;

    /**
     * the HTTP method used to send the request
     * @var String 
     */
    private $_httpMethod = 'GET';

    /**
     * The data to send with the request. Typically used with the POST http 
     * method
     * @var Mixed
     */
    private $_dataToSend; // 

    /**
     * The mimetype of the data to send with the request. It is used to set the 
     * content-type header of the request
     * @var String 
     */
    private $_dataToSendMimetype = null; // 

    /**
     * Logger instance
     * @var Object
     */
    private $_logger;

    public function __construct($config)
    {
        $this->_config = $config;
        if (isset($this->_config->logPath)) {
            $logFile = dirname(__DIR__) . '/log/fedora-proxy.log';
        } else {
            $logFile = $this->_config->logPath . '/fedora-proxy.log';
        }
        $this->_logger = Log::factory('file', $logFile, 'RestClient');
        $mask = Log::MAX(constant($this->_config->logLevel));
        $this->_logger->setMask($mask);
    }

    /**
     * Get The URL of the REST service
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set The URL of the REST service
     * @param type $url 
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * Get the HTTP method used to send the request
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    /**
     * Set the HTTP method used to send the request
     *
     * @var $http_method string
     * @return void
     */
    public function setHttpMethod($httpMethod)
    {
        $httpMethod = strtoupper($httpMethod);
        if ($httpMethod == 'GET' || $httpMethod == 'POST' || $httpMethod == 'PUT' || $httpMethod == 'DELETE' || $httpMethod == 'HEAD' || $httpMethod == 'TRACE') {
            $this->_httpMethod = strtoupper($httpMethod);
        }
    }

    /**
     * Get the data to send with the request
     * @return mixed
     */
    public function getDataToSend()
    {
        return $this->_dataToSend;
    }

    /**
     * Set the data to send with the request
     * @param mixed $dataToSend 
     * @param string $contentMimetype The mimetype of the data to send with 
     * the request. It is used to set the content-type header of the request
     */
    public function setDataToSend($dataToSend, $contentMimetype = null)
    {
        $this->_dataToSend = $dataToSend;

        if (isset($contentMimetype)) {
            $this->_dataToSendMimetype = $contentMimetype;
        }
    }

    /**
     * Send the request by using the cURL extension
     * @return RestResult
     * @throws ErrorException 
     */
    public function sendCurlRequest()
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestClient::sendCurlRequest", PEAR_LOG_DEBUG);

        $result = new RestResult();
        $result->setRequestHttpMethod($this->_httpMethod);
        $result->setRequestSentData($this->_dataToSend);

        $urlInfo = parse_url($this->_url);
        $this->_logger->log("url: {$this->_url}", PEAR_LOG_DEBUG);

        if (isset($urlInfo['port'])) {
            $this->_logger->log("port is set in the url...", PEAR_LOG_DEBUG);
            $url = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'];

            if (isset($urlInfo['query']) && strlen($urlInfo['query']) > 0) {
                $url .= '?' . $urlInfo['query'];
            }

            $result->setRequestPort($urlInfo['port']);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_PORT, $urlInfo['port']);
        } else {
            $url = $this->_url;
            $curl = curl_init($url);
        }

        $result->setRequestUrl($url);

        $headers = array();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->_httpMethod);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        //should not wait forever !
        $time = ini_get('max_execution_time');
        $this->_logger->log("max execution time: {$time}", PEAR_LOG_DEBUG);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $time);

        if ($this->_config->checkServerCertificate) {
            $this->_logger->log('Check server certificate.. ', PEAR_LOG_DEBUG);
            $this->setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            $this->setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            $this->setopt(
                    $curl, CURLOPT_CAINFO, dirname(__DIR__) . '/ssl/' . $this->_config->serverCertificate
            );
        } else {
            $this->_logger->log('Do not check server certificate.. ', PEAR_LOG_DEBUG);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        $this->_logger->log("Follow location: true", PEAR_LOG_DEBUG);
        $this->_logger->log("Max redirections: 10", PEAR_LOG_DEBUG);
        $this->setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $this->setopt($curl, CURLOPT_MAXREDIRS, 10);

        $login = $this->_config->login;
        $password = $this->_config->password;

        $this->_logger->log("login: {$login}", PEAR_LOG_DEBUG);
        $this->_logger->log("password: ********", PEAR_LOG_DEBUG);

        if (!empty($login)) {
            curl_setopt($curl, CURLOPT_USERPWD, "$login:$password");
        }

        if (isset($this->_dataToSend)) {
            $this->_logger->log("Send data...", PEAR_LOG_DEBUG);
            curl_setopt($curl, CURLOPT_POST, true);

            if (is_string($this->_dataToSend)) {
                $this->_logger->log("dataToSend: {$this->_dataToSend}", PEAR_LOG_DEBUG);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_dataToSend);
            } elseif (is_array($this->_dataToSend)) {
                if (isset($this->_dataToSend['content'])) {
                    $this->_logger->log("dataToSend: content", PEAR_LOG_DEBUG);
                    /*
                     * If $this->dataToSend is an array and the content to send
                     * is in $this->dataToSend['content'], we use it
                     */
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_dataToSend['content']);
                } elseif (isset($this->_dataToSend['file'])) {
                    /*
                     * In case of a file to send, the upload works with an array.
                     * The value of the file must begin with an '@'
                     * e.g: $this->dataToSend['file'] --> 
                     * array('myDocument.pdf' => '@/path/to/file')
                     */
                    $this->_logger->log("dataToSend: file", PEAR_LOG_DEBUG);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_dataToSend['file']);
                }

                /*
                 * If the mime type is given as a parameter, we use it to set 
                 * the content-type request
                 */
                if (isset($this->_dataToSend['mimeType'])) {
                    $this->_logger->log("mimeType: {$this->_dataToSend['mimeType']}", PEAR_LOG_DEBUG);
                    $this->_dataToSendMimetype = $this->_dataToSend['mimeType'];
                }
            }
        }

        if (isset($this->_dataToSendMimetype)) {
            $headers[] = 'Content-type: ' . $this->_dataToSendMimetype;
        }

        if (count($headers) > 0) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        return $this->executeRequest($curl, $result);
    }

    /**
     * Upload file
     * @param array $postData
     * @return type
     * @throws InvalidArgumentException 
     */
    public function uploadFile(array $postData)
    {

        $result = new RestResult();

        if (empty($postData)) {
            throw new InvalidArgumentException("postData is empty!");
        }

        $username = $this->_config->login;
        $password = $this->_config->password;
        $url = $this->_config->baseUrl;
        $url .= "/management/upload";

        $curl = curl_init();
        // Set URL on which you want to post the Form and/or data
        curl_setopt($curl, CURLOPT_URL, $url);
        // Set username password
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
        // Data+Files to be posted
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);

        // Pass TRUE or 1 if you want to wait for and catch the response against 
        // the request made
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // For Debug mode; shows up any error encountered during the operation
        curl_setopt($curl, CURLOPT_VERBOSE, 1);

        $result = $this->executeRequest($curl, $result);
        return $result->getResponseContent();
    }

    protected function executeRequest($curl, $result)
    {
        try {
            $responseContent = curl_exec($curl);
            $responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $responseMimeType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
            $responseError = curl_error($curl);

            $result->setResponseContent($responseContent);
            $result->setResponseHttpCode($responseHttpCode);
            $result->setResponseMimeType($responseMimeType);
        } catch (ErrorException $exception) {
            curl_close($curl);
            throw $exception;
        }
        curl_close($curl);

        if (isset($responseError) && strlen($responseError) > 0) {
            $this->_logger->log('Resonse Error:  ' . $responseError, PEAR_LOG_ERR);
            $result->setResponseError($responseError);
        } elseif ($responseHttpCode < 200 || $responseHttpCode >= 300) {
            $this->_logger->log('Response:  ' . $responseHttpCode, PEAR_LOG_DEBUG);
            $result->setResponseError('The REST request returned an HTTP error code of '
                    . $responseHttpCode . ' (' . $this->getHttpCodeTranslation($responseHttpCode) . ')');
        }

        return $result;
    }

    protected function setopt($curl, $key, $value)
    {
        if (!empty($value)) {
            return curl_setopt($curl, $key, $value);
        } else {
            return false;
        }
    }

    /**
     * Returns human readable http status code
     * @param String $httpCode http status code
     * @return string|null 
     */
    public function getHttpCodeTranslation($httpCode)
    {
        switch ($httpCode) {
            case '400':
                return 'Bad Request';
            case '401':
                return 'Unauthorized';
            case '402':
                return 'Payment Required';
            case '403':
                return 'Forbidden';
            case '404':
                return 'Not Found';
            case '405':
                return 'Method Not Allowed';
            case '406':
                return 'Not Acceptable';
            case '407':
                return 'Proxy Authentication Required';
            case '408':
                return 'Request Time-out';
            case '409':
                return 'Conflict';
            case '410':
                return 'Gone';
            case '411':
                return 'Length Required';
            case '412':
                return 'Precondition Failed';
            case '413':
                return 'Request Entity Too Large';
            case '414':
                return 'Request-URI Too Long';
            case '415':
                return 'Unsupported Media Type';
            case '416':
                return 'Requested range unsatisfiable';
            case '417':
                return 'Expectation failed';
            case '422':
                return 'Unprocessable entity';
            case '423':
                return 'Locked';
            case '424':
                return 'Method failure';
            case '500':
                return 'Internal Server Error';
            case '501':
                return 'Not Implemented';
            case '502':
                return 'Bad Gateway ou Proxy Error';
            case '503':
                return 'Service Unavailable';
            case '504':
                return 'Gateway Time-out';
            case '505':
                return 'HTTP Version not supported';
            case '507':
                return 'Insufficient storage';
            case '509':
                return 'Bandwidth Limit Exceeded';
            default:
                return null;
        }
    }

}
