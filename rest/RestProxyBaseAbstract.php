<?php

require_once dirname(__FILE__) . '/Config.php';
require_once dirname(__FILE__) . '/RestClient.php';
require_once('Log.php');

/**
 * RestProxyBase class file.
 * 
 * Base class for proxies calling REST web methods.
 * 
 * @author    Nicolas Rod
 * @author    Laurent Opprecht <laurent.opprecht@unige.ch>
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @author    Martin Helfer <martin.helfer@ub.uni-freiuburg.de>
 * @copyright 2012 Freiburg University Library
 * @copyright 2010 University of Geneva
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 */
abstract class RestProxyBaseAbstract
{

    protected $_restClient = null;
    protected $_config = null;
    protected $_logger;

    public function __construct()
    {
        $this->_config = new Config;

        if (isset($this->_config->logPath)) {
            $logFile = dirname(__DIR__) . '/log/fedora-proxy.log';
        } else {
            $logFile = $this->_config->logPath . '/fedora-proxy.log';
        }
        $this->_logger = Log::factory('file', $logFile, 'RestProxyBaseAbstract');
        $mask = Log::MAX(constant($this->_config->logLevel));
        $this->_logger->setMask($mask);
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function setConfig($value)
    {
        $this->_config = $value;
    }

    protected function getRestClient()
    {
        if (!empty($this->_restClient)) {
            return $this->_restClient;
        } else {
            $result = new RestClient($this->_config);
            return $this->_restClient = $result;
        }
    }

    public function execute($verb, $parameters = array(), $httpMethod = 'get',
        $dataToSend = null, $mimeType = '')
    {
        try {
            $this->_logger->log('---------', PEAR_LOG_DEBUG);
            $this->_logger->log("RestProxyBaseAbstract::execute", PEAR_LOG_DEBUG);
            $this->_logger->log("httpMethod: {$httpMethod}", PEAR_LOG_DEBUG);
            $this->_logger->log("dataToSend: {$dataToSend}", PEAR_LOG_DEBUG);
            $this->_logger->log("mimeType: {$mimeType}", PEAR_LOG_DEBUG);

            $base = $this->_config->baseUrl;
            $base = rtrim($base, '/');

            $this->_logger->log('base: ' . $base, PEAR_LOG_DEBUG);
            $args = array();
            foreach ($parameters as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $args[] = $key . '=' . urlencode($value);
            }
            $args = implode('&', $args);
            $args = empty($args) ? '' : "?$args";

            $url = "$base/{$verb}{$args}";

            $result = $this->getRestXmlResponse($url, $httpMethod, $dataToSend,
                $mimeType);
            return $result;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function executePost($verb, $post = array())
    {
        try {
            $this->_logger->log('---------', PEAR_LOG_DEBUG);
            $this->_logger->log('RestProxyBaseAbstract::executePost',
                PEAR_LOG_DEBUG);
            $base = $this->_config->baseUrl;
            $base = rtrim($base, '/');
            $url = "$base/{$verb}";
            $result = $this->getPostXmlResponse($url, $post);
            return $result;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Sends a request via API-M and returns the pid.
     * @param String $verb path (e.g. objects/<pid>/datastreams/<dsID>/content)
     * @param Array $parameters with parameters (e.g. label, pid)
     * @param String $httpMethod GET, POST
     * @param type $dataToSend
     * @param type $mimeType
     * @return String current time
     * @throws Exception 
     */
    public function executeRaw($verb, $httpMethod, array $parameters = array(),
        $dataToSend = null, $mimeType = '')
    {
        try {
            $this->_logger->log('---------', PEAR_LOG_DEBUG);
            $this->_logger->log("RestProxyBaseAbstract::executeRaw",
                PEAR_LOG_DEBUG);
            $this->_logger->log("verb: {$verb}", PEAR_LOG_DEBUG);
            $this->_logger->log("httpMethod: {$httpMethod}", PEAR_LOG_DEBUG);
            $this->_logger->log("mimeType: {$mimeType}", PEAR_LOG_DEBUG);

            $base = $this->_config->baseUrl;
            $base = rtrim($base, '/');
            $args = array();
            foreach ($parameters as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $args[] = $key . '=' . urlencode($value);
                $this->_logger->log("parameter [key:value]: {$key}:{$value}",
                    PEAR_LOG_DEBUG);
            }
            $args = implode('&', $args);
            $args = $args ? '?' . $args : '';
            $url = "$base/$verb{$args}";
            $this->_logger->log("url {$url}", PEAR_LOG_DEBUG);
            return $this->getRestResponse($url, $httpMethod, $dataToSend,
                    $mimeType);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function executePostRaw($verb, $post = array())
    {
        try {
            $this->_logger->log('---------', PEAR_LOG_DEBUG);
            $this->_logger->log("RestProxyBaseAbstract::executePostRaw",
                PEAR_LOG_DEBUG);
            $this->_logger->log("verb: {$verb}", PEAR_LOG_DEBUG);

            $base = $this->_config->baseUrl;
            $base = rtrim($base, '/');
            $url = "$base/{$verb}";

            $result = $this->getPostResponse($url, $post);
            return $result;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Send a request to a REST service and parse the response as an XML Document
     * @param $url string
     * @param $httpMethod string
     * @param $dataToSend string The content to send with the REST request
     * @param $contentMimetype The mimetype of the content to send with the REST request
     * @return DOMDocument or null if the response is not well formed XML
     */
    protected function getRestXmlResponse($url, $httpMethod, $dataToSend = null,
        $contentMimetype = null)
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestProxyBaseAbstract::getRestXmlResponse",
            PEAR_LOG_DEBUG);
        $this->_logger->log("url: {$url}", PEAR_LOG_DEBUG);
        $this->_logger->log("httpMethod: {$httpMethod}", PEAR_LOG_DEBUG);
        $this->_logger->log("dataToSend: {$dataToSend}", PEAR_LOG_DEBUG);
        $this->_logger->log("contentMimeType: {$contentMimetype}",
            PEAR_LOG_DEBUG);

        $client = $this->getRestClient();
        $client->setUrl($url);
        $client->setHttpMethod($httpMethod);

        if (!empty($dataToSend)) {
            if (!is_array($dataToSend) && file_exists($dataToSend) && empty($contentMimetype)) {
                $contentMimetype = $this->getFileMimetype($dataToSend);
            } else {
                $contentMimetype = null;
            }
            $client->setDataToSend($dataToSend, $contentMimetype);
        }

        $result = $client->sendCurlRequest();
        $responseContent = $result->getResponseContent();

        $this->_logger->log("responseContent: {$responseContent}",
            PEAR_LOG_DEBUG);
        if (!$result->hasError() && stripos($responseContent, 'Exception') === false) {
            $this->_logger->log("result is ok...", PEAR_LOG_DEBUG);
            $document = new DOMDocument();
            if (!empty($responseContent)) {
                set_error_handler(array($this, 'handleXmlError'));
                $document->loadXML($responseContent);
                restore_error_handler();
            }
            return $document;
        } else {
            if (stripos($responseContent, 'Exception') === false) {
                throw new Exception(htmlentities($result->getResponseError()));
            } else {
                throw new Exception(
                    'REST response:URL : ' . $result->getRequestUrl() .
                    'POST data : ' . $result->getRequestSentData() .
                    'Response :' . $responseContent
                );
            }
        }
    }

    protected function getPostXmlResponse($url, $post)
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestProxyBaseAbstract::getPostXmlResponse",
            PEAR_LOG_DEBUG);
        $this->_logger->log("url: {$url}", PEAR_LOG_DEBUG);
        $this->_logger->log("post: {$post}", PEAR_LOG_DEBUG);

        $client = $this->getRestClient();
        $client->setUrl($url);
        $client->setHttpMethod('post');
        $client->setDataToSend(array('content' => $post));
        $result = $client->sendCurlRequest();

        $responseContent = $result->getResponseContent();

        if (!$result->hasError() && stripos($responseContent, 'Exception') === false) {
            $document = new DOMDocument();
            $this->_logger->log("result is ok...", PEAR_LOG_DEBUG);
            if (!empty($responseContent)) {
                set_error_handler(array($this, 'handleXmlError'));
                $document->loadXML($responseContent);
                restore_error_handler();
            }
            return $document;
        } else {
            if (stripos($responseContent, 'Exception') === false) {
                throw new Exception(htmlentities($result->getResponseError()));
            } else {
                throw new Exception(
                    'REST response: URL:' . $result->getRequestUrl() .
                    'POST data: ' . htmlentities($result->getRequestSentData()) .
                    'Response: ' . $responseContent
                );
            }
        }
    }

    /**
     * Send a request to a REST service and return the response
     *
     * @param $url string
     * @param $httpMethod string
     * @param $dataToSend string The content to send with the REST request
     * @param $contentMimetype The mimetype of the content to send with the 
     * REST request
     * @return mixed
     */
    protected function getRestResponse($url, $httpMethod, $dataToSend = null,
        $contentMimetype = null)
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestProxyBaseAbstract::getRestResponse",
            PEAR_LOG_DEBUG);
        $this->_logger->log("url: {$url}", PEAR_LOG_DEBUG);
        $this->_logger->log("httpMethod: {$httpMethod}", PEAR_LOG_DEBUG);
        $this->_logger->log("contentMimetype: {$contentMimetype}",
            PEAR_LOG_DEBUG);

        $client = $this->getRestClient();
        $client->setUrl($url);
        $client->setHttpMethod($httpMethod);

        if ($dataToSend) {
            if (empty($contentMimetype) && file_exists($dataToSend)) {
                $contentMimetype = $this->getFileMimetype($dataToSend);
            }
            $client->setDataToSend($dataToSend, $contentMimetype);
        }

        $result = $client->sendCurlRequest();

        $responseContent = $result->getResponseContent();
        $this->_logger->log("responseContent: $responseContent", PEAR_LOG_DEBUG);
        if (!$result->hasError() && stripos($responseContent, 'Exception') === false) {
            $this->_logger->log("result is ok...", PEAR_LOG_DEBUG);
            return $responseContent;
        } else {
            if (stripos($responseContent, 'Exception') === false) {
                throw new Exception($result->getResponseError());
            } else {
                throw new Exception(
                    'REST response:' . $result->getRequestUrl() .
                    ', POST data: ' . htmlentities($result->getRequestSentData()) .
                    ', Response: ' . $responseContent
                );
            }
        }
    }

    protected function getPostResponse($url, $post)
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestProxyBaseAbstract::getPostResponse",
            PEAR_LOG_DEBUG);
        $this->_logger->log("url: {$url}", PEAR_LOG_DEBUG);
        $this->_logger->log("post: {$post}", PEAR_LOG_DEBUG);

        $client = $this->getRestClient();
        $client->setUrl($url);
        $client->setHttpMethod('post');
        $client->setDataToSend(array('content' => $post));

        $result = $client->sendCurlRequest();

        $responseContent = $result->getResponseContent();

        if (!$result->hasError() && stripos($responseContent, 'Exception') === false) {
            $this->_logger->log("result is ok...", PEAR_LOG_DEBUG);
            return $responseContent;
        } else {
            if (stripos($responseContent, 'Exception') === false) {
                throw new Exception($result->getResponseError());
            } else {
                throw new Exception(
                    'REST response: URL : ' . $result->getRequestUrl() .
                    'POST data:' . $result->getRequestSentData() .
                    'Response:' . $responseContent
                );
            }
        }
    }

    /**
     * Ingesting local datastreams to Fedora may be accomplished using the 
     * semi-secret upload servlet.
     * If you POST content to:
     * http://host:port/fedora/management/upload
     * 
     * The response received will be a temporary URI that may be passed into 
     * the other management APIs for ingestion or datastream creation. Fedora 
     * will resolve that URI into the datastream that was "POST"ed. Note that 
     * the data will only be retained on the server for a short time, so beware 
     * of timing problems — the default timeout is five minutes. You can set a 
     * higher valueby adding the uploadStorageMinutes param to your 
     * fedora.fcfg. This parameter goes in the Management module's 
     * configuration section, and specifies the number of minutes after which 
     * uploaded content will be automatically deleted if not used. 
     * 
     * @param String $file path + name of the file to be uploaded
     * @return url of the temporary saved file 
     */
    public function upload($file)
    {
        $this->_logger->log('---------', PEAR_LOG_DEBUG);
        $this->_logger->log("RestProxyBaseAbstract::upload", PEAR_LOG_DEBUG);
        $this->_logger->log("file: {$file}", PEAR_LOG_DEBUG);

        if (empty($file)) {
            throw new InvalidArgumentException('$file must be set!');
        }

        if (!file_exists($file)) {
            throw new InvalidArgumentException('file does not exists!');
        }
        $client = $this->getRestClient();
        $postData = array("file" => "@" . $file);

        return $client->uploadFile($postData);
    }

    /**
     * Handles XML error.
     * loadXml reports an error instead of throwing an exception when the xml 
     * is not well formed. This is annoying if you are trying to to loadXml() 
     * in a try...catch statement. Apparently its a feature, not a bug, 
     * because this conforms to a spefication. 
     * See also: http://php.net/manual/de/domdocument.loadxml.php
     * 
     * @param type $errorNo
     * @param type $errorStr
     * @param type $errorFile
     * @param type $errorLine
     * @return boolean
     * @throws DOMException 
     */
    public function handleXmlError($errorNo, $errorStr, $errorFile, $errorLine)
    {
        if ($errorNo == E_WARNING && substr_count($errorStr, 'DOMDocument') > 0) {
            throw new DOMException("File: {$errorFile}, At line: {$errorLine}, Message: {$errorStr}");
        } else {
            return false;
        }
    }

}
