<?php

require_once dirname(__FILE__) . '/../FedoraProxy.php';

/**
 * FedoraProxyTest class file. 
 * Notice: Test class for the class FedoraProxy. Tests needs a working Fedora-Server!
 * 
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @author    Martin Helfer <martin.helfer@ub.uni-freiburg.de>
 * @copyright 2012 Freiburg University Library
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 */
class FedoraProxyTest extends PHPUnit_Framework_TestCase {

    /**
     * @var FedoraProxy
     */
    private $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new FedoraProxy;
        $pid = 'fedoraProxyTest:1';
        if ($this->object->exists($pid)) {
            $this->object->purgeObject($pid);
        }

        $dcTitle = 'fedoraProxyTest 1';
        $label = 'fedoraProxyTest 1';
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';
        $this->object->ingest($xml_content, $pid, $label);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object->purgeObject('fedoraProxyTest:1');
    }

    /**
     * @covers FedoraProxy::findObjects
     */
    public function testFindObjects() {
        // Existing object
        $parameters = array("terms" => "fedoraProxyTest",
            "pid" => "true",
            "label" => "true",
            "resultFormat" => "xml");
        $result = $this->object->findObjects($parameters);
        $this->assertTrue(is_string($result->xmlVersion));

        // Not existing object
        $parameters = array("terms" => "thisdoesnotexist",
            "pid" => "true",
            "label" => "true",
            "resultFormat" => "xml");
        $result = $this->object->findObjects($parameters);
        $this->assertTrue(is_string($result->xmlVersion));
    }

    /**
     * @covers FedoraProxy::getDatastreamContent
     */
    public function testGetDatastreamContent() {
        // existing object
        $pid = "fedoraProxyTest:1";
        $dsID = "DC";
        $result = $this->object->getDatastreamContent($pid, $dsID);
        $this->assertTrue(is_string($result));
    }

    /**
     * @covers FedoraProxy::getDatastreamContent 
     */
    public function testGetDataStreamContentWithWrongPid() {
        $pid = "notExistingObject:1";
        $dsID = "DC";
        try {
            $this->object->getDatastreamContent($pid, $dsID);
        } catch (Exception $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @covers FedoraProxy::ingest
     */
    public function testIngest() {
        $pid = 'fedoraProxyTest:2';
        $dcTitle = 'fedoraProxyTest 2';
        $label = 'fedoraProxyTest 2';
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';
        $result = $this->object->ingest($xml_content, $pid, $label);

        $this->assertEquals("fedoraProxyTest:2", $result);
        $result = $this->object->purgeObject('fedoraProxyTest:2');
    }

    /**
     * @covers FedoraProxy::purgeObject
     */
    public function testPurgeObjectWithExistingObject() {
        $pid = 'fedoraProxyTest:2';
        $dcTitle = 'fedoraProxyTest 2';
        $label = 'fedoraProxyTest 2';
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';
        $this->object->ingest($xml_content, $pid, $label);
        $result = $this->object->purgeObject('fedoraProxyTest:2');
        $this->assertEquals(1, preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}.*Z/", $result));
    }

    /**
     * @covers FedoraProxy::purgeObject
     */
    public function testPurgeObjectWithNotExistingObject() {
        try {
            $this->object->purgeObject('NotExistingObject:1');
        } catch (Exception $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @covers FedoraProxy::upload     
     */
    public function testUpload() {
        $file = 'data/1.csv';
        try {
            $result = $this->object->upload($file);
            $this->assertTrue(is_string($result));
            $this->assertEquals(1, preg_match("/uploaded:\/\/[0-9]*/", $result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @covers FedoraProxy::upload
     * @expectedException InvalidArgumentException
     */
    public function testUploadWithEmptyFile() {
        $file = null;
        $mimetype = 'text';
        $this->object->upload($file, $mimetype);
    }

    /**
     * @covers FedoraProxy::upload
     * @expectedException InvalidArgumentException
     */
    public function testUploadWithWrongFilePath() {
        $file = '/this/path/does/not/exists/test.txt';
        $this->object->upload($file);
    }

    /**
     * @covers FedoraProxy::uploadNewDatastream
     */
    public function testUploadNewDatastream() {
        $object = new FedoraProxy;
        $pid = 'fedoraProxyTest:3';
        if ($object->exists($pid)) {
            $object->purgeObject($pid);
        }

        $dcTitle = 'fedoraProxyTest 3';
        $label = 'fedoraProxyTest 3';
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';
        $object->ingest($xml_content, $pid, $label);

        // Reset the FedoraProxy object
        $object = null;
        $object = new FedoraProxy;
        $file = 'data/1.csv';
        $pid = "fedoraProxyTest:3";
        $dsID = "csv";
        $dsLabel = "CSV Test";
        $mimeType = "text/csv";
        $result = $object->uploadNewDataStream($file, $pid, $dsID, $dsLabel, $mimeType);
        $this->assertTrue(is_string($result));
    }

    /**
     * @covers FedoraProxy::exists
     */
    public function testExists() {
        $pid = "fedoraProxyTest:1";
        $this->assertTrue($this->object->exists($pid));

        $pid = "notExistingCollection:1";
        $this->assertFalse($this->object->exists($pid));
    }

    /**
     * @covers FedoraProxy::purgeNamespace
     */
    public function testPurgeNamespace() {
        $object = new FedoraProxy;
        $namespace = "changeme";
        $pid = "{$namespace}:6";
        if ($object->exists($pid)) {
            $object->purgeObject($pid);
        }

        $dcTitle = "{$namespace} 6";
        $label = "{$namespace} 6";
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';
        $object->ingest($xml_content, $pid, $label);

        $pid = "{$namespace}:7";
        if ($object->exists($pid)) {
            $object->purgeObject($pid);
        }

        $dcTitle = "{$namespace} 7";
        $label = "{$namespace} 7";

        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';

        $object->ingest($xml_content, $pid, $label);


        $pid = "{$namespace}:8";
        if ($object->exists($pid)) {
            $object->purgeObject($pid);
        }

        $dcTitle = "{$namespace} 8";
        $label = "{$namespace} 8";

        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
                        <foxml:digitalObject xmlns:foxml="info:fedora/fedora-system:def/foxml#" VERSION="1.1" PID="' . $pid . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd">
                        <foxml:objectProperties>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#state" VALUE="Active"/>
                        <foxml:property NAME="info:fedora/fedora-system:def/model#label" VALUE="' . $label . '"/>
                        </foxml:objectProperties>
                        <foxml:datastream CONTROL_GROUP="X" ID="DC" STATE="A" VERSIONABLE="true">
                        <foxml:datastreamVersion FORMAT_URI="http://www.openarchives.org/OAI/2.0/oai_dc/" ID="DC1.0" LABEL="DC" MIMETYPE="text/xml">
                        <foxml:xmlContent>
                        <oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
                        <dc:title>' . $dcTitle . '</dc:title>
                        </oai_dc:dc>
                        </foxml:xmlContent>
                        </foxml:datastreamVersion>
                        </foxml:datastream>
                        </foxml:digitalObject>';

        $object->ingest($xml_content, $pid, $label);

        $object->purgeNamespace($namespace);
        $this->assertFalse($object->findNamespace($namespace));
    }

    /**
     * @covers FedoraProxy::findNamespace
     */
    public function testFindNamespace() {
        $object = new FedoraProxy;
        $this->assertTrue($object->findNamespace("fedoraProxyTest"));
        $this->assertFalse($object->findNamespace("notExisting"));
    }

    /**
     * @covers FedoraProxy::getNextPid
     * How to test?
     */
    public function testGetNextPid() {
        $object = new FedoraProxy;
        echo $object->getNextPid("lopes");
    }

}

?>
