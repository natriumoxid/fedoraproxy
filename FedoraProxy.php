<?php

require_once dirname(__FILE__) . '/rest/RestProxyBaseAbstract.php';

/**
 * FedoraProxy class file.
 * 
 * Interface to Fedora's REST based API-MA (see also 
 * https://wiki.duraspace.org/display/FEDORA36/REST+API)
 * 
 * @author    Laurent Opprecht <laurent.opprecht@unige.ch>
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @author    Martin Helfer <martin.helfer@ub.uni-freiburg.de>
 * @copyright 2012,2013 Freiburg University Library
 * @copyright 2010 University of Geneva
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @see       https://wiki.duraspace.org/display/FEDORA35/REST+API
 */
class FedoraProxy extends RestProxyBaseAbstract
{

    /**
     * Finds objects.
     *  
     * URL Syntax: /objects ? [terms | query] [maxResults] [resultFormat] [pid]
     *                        [label] [state] [ownerId] [cDate]  [mDate] 
     *                        [dcmDate] [title] [creator] [subject] 
     *                        [description] [publisher] [contributor] [date] 
     *                        [type] [format] 
     * [identifier] [source] [language] [relation] [coverage] [rights]
     * HTTP Method: GET
     * HTTP Response: 200
     * 
     * @param array $parameters Parameters is on of the follwing parameter:
     * terms: a phrase represented as a sequence of characters (including the ? 
     *        and * wildcards) for the search. If this sequence is found in any
     *        of the fields for an object, the object is considered a match. 
     *        Do NOT use this parameter in combination with the 
     *        "query" parameter.	 
     * query: a sequence of space-separated conditions. A condition consists of 
     *        a metadata element name followed directly by an operator, 
     *        followed directly by a value. Valid element names are (pid, label,
     *        state, ownerId, cDate, mDate, dcmDate, title, creator, subject, 
     *        description, publisher, contributor, date, type, 
     *        format, identifier, source, language, relation, coverage, rights).
     *        Valid operators are: contains (),  equals (=), greater than (>), 
     *        less than (<), greater than or equals (>=), less than or equals 
     *        (<=). The contains () operator may be used in combination with the
     *        ? and * wildcards to query for simple string patterns. 
     *        Space-separators should be encoded in the URL as %20. Operators 
     *        must be encoded when used in the URL syntax as follows: the (=) 
     *        operator must be encoded as %3D, the (>) operator as %3E, the (<) 
     *        operator as %3C, the (>=) operator as %3E%3D, the (<=) operator as
     *        %3C%3D, and the (~) operator as %7E. Values may be any string. If 
     *        the string contains a space, the value should begin and end with a
     *        single quote character ('). If all conditions are met for an 
     *        object, the object is considered a match. Do NOT use this 
     *        parameter in combination with the "terms" parameter 
     * maxResults [number]: the maximum number of results that the server should
     * provide at once. If this is unspecified, the server will default to a 
     * small value .
     * 
     * resultFormat [xml, html]: the preferred output format 
     * 
     * pid  [false,true]: if true, the Fedora persistent identifier (PID) 
     *                    element of matching objects will be included in the 
     *                    response
     * 
     * label  [false, true]: if true, the Fedora object label element of 
     * matching objects will be included in the response
     * 
     * state  [false, true]: if true, the Fedora object state element of 
     * matching objects will be included in the response
     * 
     * ownerId  [false, true]: if true, each matching objects' owner id will be 
     * included in the response
     * 
     * cDate[false, true]: if true, the Fedora create date element of matching 
     * objects will be included in the response
     * 
     * mDate[false, true]: if true, the Fedora modified date of matching objects
     * will be included in the response
     * 
     * dcmDate[false, true]: if true, the Dublin Core modified date element(s) 
     * of matching objects will be included in the response
     * 
     * title[false, true]: if true, the Dublin Core title element(s) of matching
     * objects will be included in the response
     * 
     * creator[false, true]: if true, the Dublin Core creator element(s) of 
     * matching objects will be included in the response
     * 
     * subject[false, true]: if true, the Dublin Core subject element(s) of 
     * matching objects will be included in the response
     * 
     * description[false, true]: if true, the Dublin Core description element(s)
     * of matching objects will be included in the response
     * 
     * publisher[false, true]: if true, the Dublin Core publisher element(s) of 
     * matching objects will be included in the response
     * 
     * contributor[false, true]: if true, the Dublin Core contributor element(s)
     * of matching objects will be included in the response
     * 
     * date[false, true]: if true, the Dublin Core date element(s) of matching 
     * objects will be included in the response
     * 
     * type[false, true]: if true, the Dublin Core type element(s) of matching 
     * objects will be included in the response
     * 
     * format[false, true]: if true, the Dublin Core format element(s) of 
     * matching objects will be included in the response
     * 
     * identifier[false, true]: if true, the Dublin Core identifier element(s) 
     * of matching objects will be included in the response
     * 
     * source[false, true]: if true, the Dublin Core source element(s) of 
     * matching objects will be included in the response
     * 
     * language[false, true]: if true, the Dublin Core language element(s) of 
     * matching objects will be included in the response
     * 
     * relation[false, true]: if true, the Dublin Core relation element(s) of
     * matching objects will be included in the response
     * 
     * coverage[false, true]: if true, the Dublin Core coverage element(s) of 
     * matching objects will be included in the response
     * 
     * rights[false, true]: if true, the Dublin Core rights element(s) of 
     * matching objects will be included in the response
     * 
     * Examples
     * /objects?terms=demo&pid=true&subject=true&label=true&resultFormat=xml
     * /objects?query=title%7Erome%20creator%7Estaples&pid=true&title=true&creator=true
     * /objects?query=pid%7E*1&maxResults=50&format=xml&pid=true&title=true
     * 
     * @return mixed 
     * @throws Exception  
     */
    final public function findObjects(array $parameters = array())
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::findObjects', PEAR_LOG_DEBUG);
        try {
            if (!isset($parameters['maxResults'])) {
                $parameters['maxResults'] = $this->_config->maxResults;
            }

            $result = $this->execute('objects', $parameters, 'get');
            return $result;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function findNamespace($namespace)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::findNamespace', PEAR_LOG_DEBUG);

        $parameters = array("query" => "pid~{$namespace}:*", "pid" => "true",
                "resultFormat" => "xml");


        $dom = $this->findObjects($parameters);
        $pidElement = $dom->getElementsByTagName('pid')->item(0);
        var_dump($pidElement);

        if (empty($pidElement)) {
            $this->_logger->log("findNamespace: namespace {$namespace} was found", PEAR_LOG_DEBUG);
            return false;
        } else {
            $this->_logger->log("findNamespace: namespace {$namespace} found", PEAR_LOG_DEBUG);
            return true;
        }
    }

    //FOXML erzeugen
    final public function createFoxml($pid, $label, $xml, $state)
    {

        //FOXML erzeugen
        $output = '<?xml version="1.0" encoding="UTF-8"?>
                   <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                     <foxml:objectProperties>
                       <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="' . $state . '"/>
                       <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                     </foxml:objectProperties>
                     <foxml:datastream ID="METADATA" STATE="A" CONTROL_GROUP="X">
                       <foxml:datastreamVersion ID="METADATA.0" MIMETYPE="text/xml" LABEL="METADATA">
                         <foxml:xmlContent>' . $xml . '</foxml:xmlContent>
                       </foxml:datastreamVersion>
                     </foxml:datastream>
                   </foxml:digitalObject>';

        //XML formatieren
        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($output);
        $dom->formatOutput = TRUE;
        return $dom->saveXML();
    }

    //  Naechste PID eines Namesraums holen
    //  /objects/nextPID ? [numPIDs] [namespace] [format]
    //  /objects/nextPID?numPIDs=5&namespace=test&format=xml
    final public function getNextPid($namespace)
    {

        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::getNextPid', PEAR_LOG_DEBUG);

        try {

            //Naechste PID holen
            $res = $this->executeRaw("objects/nextPID?numPIDs=1&namespace=" . $namespace . "&format=xml", 'POST');

            //Anwort auslesen
            $doc = new DOMDocument;
            $doc->loadXML($res);

            //PID zurueckgeben
            return $doc->getElementsByTagName("pid")->item(0)->nodeValue;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Returns Datastream as XML
     * URL Syntax: /objects/{pid}/datastreams/{dsID} ? [asOfDateTime] [format]
     * [validateChecksum]
     * HTTP Method: GET
     * HTTP Response: 200
     * Examples:/objects/demo:29/datastreams/DC
     *          /objects/demo:29/datastreams/DC?format=xml
     *          /objects/demo:29/datastreams/DC?format=xml&validateChecksum=true

     * @param string $pid persistent identifier of the digital object
     * @param string $dsID datastream identifier
     * @return string | null
     */
    final public function getDatastreamContent($pid, $dsID)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::getDatastreamContent', PEAR_LOG_DEBUG);
        try {
            return $this->executeRaw(
                    "objects/$pid/datastreams/$dsID/content", 'GET'
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Creates new object using API-M injest. 
     * 
     * @param type $xmlContent file to be ingested as a new object 	
     * @param String $pid persistent identifier of the object to be created, if 
     * pid is 0 create new PID
     * @param String $label the label of the new object 
     * @param String $ownerId the id of the user to be listed at the object 
     * owner 
     * @param type $format the XML format of the object to be ingested 
     * (info:fedora/fedora-system:FOXML-1.1, 
     * info:fedora/fedora-system:FOXML-1.0, 
     * info:fedora/fedora-system:METSFedoraExt-1.1, 
     * info:fedora/fedora-system:METSFedoraExt-1.0, 
     * info:fedora/fedora-system:ATOM-1.1, 
     * info:fedora/fedora-system:ATOMZip-1.1 
     * @param String $encoding the encoding of the XML to be ingested. If this 
     * is specified, and given as anything other than UTF-8, you must ensure 
     * that the same encoding is declared in the XML.  For example, if you 
     * specify "ISO-88591" as the encoding, the XML should start with:
     * <?xml version="1.0" encoding="ISO-8859-1"?>  
     * @param String $namespace the namespace to be used to create a PID for a 
     * new empty object; if object XML is included with the request, the 
     * namespace parameter is ignored 
     * @param String $logMessage a message describing the activity being performed 
     * @param boolean $ignoreMime indicates that the request should not be 
     * checked to ensure that the content is XML prior to attempting an ingest. 
     * This is provided to allow for client applications which do not 
     * indicate the correct Content-Type when submitting a request. 
     * @return String current time
     */
    final public function ingest($xmlContent, $pid = 0, $label = '', $ownerId = '',
        $format = 'info:fedora/fedora-system:FOXML-1.1', $encoding = 'UTF-8', $namespace = '', $logMessage = '',
        $ignoreMime = false)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::ingest', PEAR_LOG_DEBUG);

        $parameters = array();
        if (!empty($label)) {
            $parameters['label'] = $label;
        }
        if (!empty($ownerId)) {
            $parameters['owner_id'] = $ownerId;
        }
        if (!empty($format)) {
            $parameters['format'] = $format;
        }
        if (!empty($encoding)) {
            $parameters['encoding'] = $encoding;
        }
        if (!empty($namespace)) {
            $parameters['namespace'] = $namespace;
        }
        if (!empty($logMessage)) {
            $parameters['logMessage'] = $logMessage;
        }
        if (!empty($ignoreMime)) {
            $parameters['ignoreMime'] = $ignoreMime;
        }

        // if pid is not set create new
        $pid = empty($pid) ? 'new' : $pid;

        //Add document 
        try {
            return $this->executeRaw(
                    "objects/$pid", 'POST', $parameters, $xmlContent, 'text/xml'
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * purgeObject
     *
     * URL Syntax: /objects/{pid} ? [logMessage]
     * HTTP Method: DELETE
     * HTTP Response: 204
     * Examples: DELETE /objects/demo:29
     *
     * @param $pid persistent identifier of the digital object
     * @param $logMessage a message describing the activity being performed
     * @return mixed: success = timestamp, e.g. 2012-04-03T14:32:46.791Z, 
     * failed = null
     */
    final public function purgeObject($pid, $logMessage = '')
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::purgeObject', PEAR_LOG_DEBUG);
        $parameters = array();

        if ($logMessage) {
            $parameters['logMessage'] = $logMessage;
        }

        try {
            return $this->executeRaw("objects/$pid", 'DELETE', $parameters);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    ///objects/{pid}/datastreams/{dsID} ? [startDT] [endDT] [logMessage]
    //Datastream einer PID loeschen
    final public function purgeDatastream($pid, $ds)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::purgeDatastream', PEAR_LOG_DEBUG);

        try {
            return $this->executeRaw("objects/$pid/datastreams/$ds", 'DELETE');
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Remove all objects from a given namespace. Use pids from the tripple store (database)
     * @param type $namespace
     */
    public function purgeNamespace($namespace)
    {

        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::purgeNamespace', PEAR_LOG_DEBUG);

        // Find all objects with namespace
        $parameters = array("terms" => $namespace,
                            "pid" => "true", 
                            "resultFormat" => "xml");

        $dom = $this->findObjects($parameters);
        $pids = $dom->getElementsByTagName('pid');
        
        // Purge all found objects
        foreach ($pids as $pid) {
            $this->_logger->log("Purge: {$pid->nodeValue}", PEAR_LOG_DEBUG);
            $this->purgeObject($pid->nodeValue, PEAR_LOG_DEBUG);
        }
        
    }

    /**
     * Updates a object
     * @param String $pid persistent identifier of the digital object 
     * @param String $state the new object state - *A*ctive, *I*nactive, or 
     * *D*eleted 
     * @param String $label the new object label 
     * @return String 
     */
    final public function modifyObject($pid, $state = false, $label = false)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::modifyObject', PEAR_LOG_DEBUG);

        $parameters = array();

        if ($state !== false) {
            $parameters['state'] = $state;
        }

        if ($label !== false) {
            $parameters['label'] = $label;
        }

        try {
            return $this->executeRaw("objects/$pid", 'PUT', $parameters);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Updates a datastream.
     * 
     * URL Syntax: /objects/{pid}/datastreams/{dsID} ? [dsLocation] [altIDs] 
     * [dsLabel] [versionable] [dsState] [formatURI] [checksumType] [checksum] 
     * [mimeType] [logMessage] [ignoreContent] [lastModifiedDate]
     * HTTP Method
     *  PUT
     * HTTP Response
     *  200
     * 
     * @param type $pid persistent identifier of the digital object 	 
     * @param type $dsID datastream identifier 
     * @param type $content
     * @param type $dsState one of "A", "I", "D" (*A*ctive, *I*nactive, 
     * *D*eleted) 
     * @param type $dsLocation
     * @param type $mimeType the MIME type of the content being added, this 
     * overrides the Content-Type request header 	 
     * @param type $ignoreContent tells the request handler to ignore any 
     * content included as part of the request, indicating that you do not 
     * intend to update the datasteam content. This is primarily provided to 
     * allow the use of client tools which always require content to be included
     * as part of PUT requests.
     * @return type 
     */
    final public function modifyDatastream($pid, $dsID, $content = false, $dsState = false, $dsLocation = false,
        $mimeType = "text/xml", $ignoreContent = false)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::modifyDatastream', PEAR_LOG_DEBUG);

        $ignoreContent = $content ? $ignoreContent : true;

        $dataToSend = $content ? $content : null;

        $mimeType = $mimeType ? $mimeType : '';

        $parameters = array();

        if ($dsLocation !== false) {
            $parameters['dsLocation'] = $dsLocation;
        }

        if ($dsState !== false) {
            $parameters['dsState'] = $dsState;
        }

        if ($ignoreContent !== false) {
            $parameters['ignoreContent'] = $ignoreContent;
        }

        // Update
        try {
            return $this->executeRaw(
                    "objects/$pid/datastreams/$dsID", 'PUT', $parameters, $dataToSend, $mimeType
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Creates a new datastream 
     *
     * URL Syntax: /objects/{pid}/datastreams/{dsID} ? [controlGroup] 
     * [dsLocation] [altIDs] [dsLabel] [versionable] 
     * [dsState] [formatURI] [checksumType] [checksum] [mimeType] [logMessage]
     * HTTP Method: POST
     * HTTP Response: 201
     * Examples : 
     * /objects/demo:29/datastreams/NEWDS?controlGroup=X&dsLabel=New 
     * (with Multipart file)
     * /objects/demo:29/datastreams/NEWDS?controlGroup=M&
     * dsLocation=http://example:80/newds&dsLabel=New
     *
     * @param String $pid persistent identifier of the digital object
     * @param String $dsID datastream identifier
     * @param type $dsLabel
     * @param type $content
     * @param type $mimeType the MIME type of the content being added, this 
     * overrides the Content-Type request header
     * @param type $versionable enable versioning of the datastream  true  
     * true, false
     * @param type $dsState one of "A", "I", "D" (*A*ctive, *I*nactive, 
     * *D*eleted)  A  A, I, D
     * @param type $controlGroup one of "X", "M", "R", or "E" (Inline *X*ML, 
     * *M*anaged Content, *R*edirect, or 
     *             *E*xternal Referenced)  X  X, M, R, E
     * @param type $dsLocation location of managed or external 
     * datastream content
     * @param type $altIDs alternate identifiers for the datastream
     * @param type $formatURI the format URI of the datastream
     * @param type $checksum the algorithm used to compute the checksum  
     * DEFAULT  DEFAULT, DISABLED, MD5, SHA-1, 
     *              SHA-256, SHA-385, SHA-512
     * @param type $logMessage a message describing the activity being performed
     * @param type $ignoreContent
     * @param type $lastModifiedDate
     * @return type 
     */
    final public function addDatastream($pid, $dsID, $dsLabel, $content = false, $mimeType = false, $versionable = true,
        $dsState = 'A', $controlGroup = 'M', $dsLocation = false, $altIDs = false, $formatURI = false,
        $checksum = false, $logMessage = false, $ignoreContent = false, $lastModifiedDate = false)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::addDatastream', PEAR_LOG_DEBUG);
        $this->_logger->log("mimeType: {$mimeType}", PEAR_LOG_DEBUG);

        $parameters = array();

        if (empty($pid)) {
            throw new InvalidArgumentException('Parameter $pid missing.');
        }

        if (empty($dsID)) {
            throw new InvalidArgumentException('Parameter $dsID missing.');
        }

        if ($dsLocation !== false) {
            $parameters['dsLocation'] = $dsLocation;
        }
        if ($dsLabel !== false) {
            $parameters['dsLabel'] = $dsLabel;
        }
        if ($altIDs !== false) {
            $parameters['altIDs'] = $altIDs;
        }

        $parameters['versionable'] = $versionable;

        if ($dsState !== false) {
            $parameters['dsState'] = $dsState;
        }
        if ($formatURI !== false) {
            $parameters['formatURI'] = $formatURI;
        }
        if ($checksum !== false) {
            $parameters['checksum'] = $checksum;
        }
        if ($logMessage !== false) {
            $parameters['logMessage'] = $logMessage;
        }
        if ($ignoreContent !== false) {
            $parameters['ignoreContent'] = $ignoreContent;
        }
        if ($lastModifiedDate !== false) {
            $parameters['lastModifiedDate'] = $lastModifiedDate;
        }
        if ($controlGroup) {
            $parameters['controlGroup'] = $controlGroup;
        }
        $dataToSend = $content ? $content : null;

        $parameters['mimeType'] = $mimeType;


        $mimeType = $mimeType ? $mimeType : '';

        try {
            return $this->executeRaw(
                    "objects/$pid/datastreams/$dsID", 'POST', $parameters, $dataToSend, $mimeType
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * uploadNewDatastream            
     * THIS IS NOT A FEDORA APIM REQUEST
     *
     * This routine combines the upload operation and the add datastream 
     * operationinto one single call. This routine also sets reasonable 
     * defaults for many of the parameters to addDatastream.
     *
     *   to uploaded file.
     * @param type $file Filename to upload and attach to object
     * @param type $pid PID of the object
     * @param type $dsID ID of the datastream
     * @param type $dsLabel Datastream Label
     * @param type $mimeType The mimetype of the datastream
     * @param type $content
     * @param type $versionable "true" or "false"
     * @param type $dsState "A", "I" or "D",  Default: A
     * @param type $controlGroup "X", "M", "R", or "E", Default: M
     * @param type $dsLocation Location where fedora can retrieve the content 
     * of the datastream
     * @param type $altIDs Alternertive ID's  (optional)
     * @param type $formatURI A URI for the namespace of the record
     * @param type $checksum value of the checksum in hexadecimal string
     * @param type $logMessage Log message (optional)
     * @param type $lastModifiedDate
     * @return type 
     */
    final public function uploadNewDatastream($file, $pid, $dsID, $dsLabel, $mimeType, $content = false,
        $versionable = true, $dsState = 'A', $controlGroup = 'M', $dsLocation = false, $altIDs = false,
        $formatURI = false, $checksum = 'SHA-1', $logMessage = 'Add datastream', $lastModifiedDate = false)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::uploadNewDatastream', PEAR_LOG_DEBUG);
        $this->_logger->log("file: {$file}", PEAR_LOG_DEBUG);
        $this->_logger->log("pid: {$pid}", PEAR_LOG_DEBUG);
        $this->_logger->log("dsID: {$dsID}", PEAR_LOG_DEBUG);
        $this->_logger->log("dsLabel: {$dsLabel}", PEAR_LOG_DEBUG);
        $this->_logger->log("mimeType: {$mimeType}", PEAR_LOG_DEBUG);
        $this->_logger->log("content: {$content}", PEAR_LOG_DEBUG);
        $this->_logger->log("versionable: {$versionable}", PEAR_LOG_DEBUG);
        $this->_logger->log("dsState: {$dsState}", PEAR_LOG_DEBUG);
        $this->_logger->log("controlGroup: {$controlGroup}", PEAR_LOG_DEBUG);
        $this->_logger->log("dsLocation: {$dsLocation}", PEAR_LOG_DEBUG);
        $this->_logger->log("altIDs: {$altIDs}", PEAR_LOG_DEBUG);
        $this->_logger->log("formatURI: {$formatURI}", PEAR_LOG_DEBUG);
        $this->_logger->log("checksum: {$checksum}", PEAR_LOG_DEBUG);
        $this->_logger->log("logMessage: {$logMessage}", PEAR_LOG_DEBUG);
        $this->_logger->log("lastModifiedDate: {$lastModifiedDate}", PEAR_LOG_DEBUG);
        // Now upload datastream or reference
        // Support uploading filename and external reference (URL)
        if (!empty($file)) {
            try {
                $url = $this->upload($file);
            } catch (InvalidArgumentException $exception) {
                throw $exception;
            }
        }

        $url = trim($url);

        if (empty($url)) {
            throw new Exception(
                'Upload failed! Fedora Server returns empty message!'
            );
        } else {
            $dsLocation = $url;
            try {
                return $this->addDatastream(
                        $pid, $dsID, $dsLabel, $content, $mimeType, $versionable, $dsState, $controlGroup, $dsLocation,
                        $altIDs
                );
            } catch (Exception $exception) {
                throw $exception;
            }
        }
    }

    /**
     * Checks if a object exists in fedora.
     * 
     * @param type $pid The persistent identifier of an object.
     * @return boolean 
     */
    final public function exists($pid)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::exists', PEAR_LOG_DEBUG);

        $parameters = array("query" => "pid={$pid}", "pid" => "true",
                "resultFormat" => "xml");
        $dom = $this->findObjects($parameters);
        $pidElement = $dom->getElementsByTagName('pid')->item(0);


        if (empty($pidElement)) {
            $this->_logger->log("exists: pid {$pid} was found", PEAR_LOG_DEBUG);
            return false;
        } else {
            $this->_logger->log("exists: pid {$pid} found", PEAR_LOG_DEBUG);
            return true;
        }
    }

    //Pruefen ob DS exisitert
    final public function hasDatastream($pid, $ds)
    {
        $this->_logger->log('========', PEAR_LOG_DEBUG);
        $this->_logger->log('FedoraProxy::listDatastreams', PEAR_LOG_DEBUG);

        //existiert DS?
        $hasDatastream = false;

        try {

            //Alle Datenstreams holen dieser PID holen
            $res = $this->execute("objects/$pid/datastreams", array("format" => "xml"), "GET");
            $nodes = $res->getElementsByTagName("datastream");

            //Ueber Ergebnisse iterieren
            foreach ($nodes as $node) {

                //Wenn passender DS gefunden wurde
                if ($ds == $node->getAttribute("dsid")) {
                    $hasDatastream = true;
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        //Ergebnis liefern
        return $hasDatastream;
    }

}