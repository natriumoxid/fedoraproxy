FedoraProxy
===========

### Description
FedoraProxy: Fedora commons REST API PHP client. Tested with Fedora commons 3.6.x and 3.7.x (see https://wiki.duraspace.org/display/FEDORA/Home).

The FedoroProxy helps you developing a fedora commons client based on php. It uses the fedora commons REST API. It uses curl for the REST communication. Follwing methods are supported:

#### API-A Methods:

- findObjects
- findNamespaces

#### API-M Methods

- createFoxml
- getNextPid
- getDatastream
- ingest
- purgeObject
- purgeDatastream
- purgeNamespace
- modifyObject
- modifyDatastream
- addDatastream


#### Other methods 

- uploadNewDatastream: This routine combines the upload operation and the add datastream operation into one single call.
This routine also sets reasonable defaults for many of the parameters to addDatastream.


### Getting started

#### Pre-installation
Make sure you have php with cURL support. On Ubuntu for example install the php5-curl package with:
```
sudo apt-get install php5-curl
```

Furthermore you need to install the PEAR package 'log' (you will also need to install PEAR):
``` 
sudo apt-get install php-pear
sudo pear install log
```

#### Install fedoraproxy
Grab a copy of the code by downloading the zip from this page - or cloning/forking the repo. E.g.:

```
mkdir -p /opt/fedoraProxy
cd /opt/fedoraProxy
git clone https://github.com/ubfr-franckborel/fedoraproxy.git
```

#### Conigure FedoraProxy
Make a copy of the original configuration file :
```
cd /opt/fedoraProxy/config
cp main.php.dist main.php
```

Edit the configuration file
+ logLevel: possible values PEAR_LOG_INFO (Informational), PEAR_LOG_DEBUG (a lot of logging)
+ logPath: where the logging should be logged
+ baseUrl: the address of your fedora commons server
+ login: the login is needed if you like to make write action. Here you set the username of the fedora commons admin.
+ password: the password of the fedora commons admin
+ checkServerCertificate: checks the server certificate. Use this if you are using ssl conection.
+ serverCertificate: path to additional CA certificates used to verify the target certificate.
+ maxResults: maximum of results accepted from the fedora commons server. 

#### Use FedoraProxy in your code
Include FedoraProxy into your PHP code for example like this:

```
include '<FEDORA_PROXY_HOME>/FedoraProxy.php'

$fedoraProxy = new FedoraProxy;

$pid = "test:1";

// Check if datastream exists
if($fedoraProxy->exists($pid){
  // do something
}

// purge object
$fedoraProxy->purgeObject($pid);
```

### Version
1.0

### Last Change 
13.01.2014

### Authors
+ Franck Borel <franck.borel@ub.uni-freiburg.de>
+ Martin Helfer <martin.helfer@ub.uni-freiburg.de>

Based on the api of the software moodle-qti-cp-fedora written for the
University of Geneva by Laurent Opprecht (<laurent.opprecht@unige.ch>, <laurent@opprecht.info>)
and Nicolas Rod (<Nicolas.Rod@unige.ch>).

### Thanks
To Laurent Opprecht and Nicolas Rod for their fedora client api.


### Copying/License
GNU General Public License - http://www.gnu.org/copyleft/gpl.html

### Bugs
Not known
