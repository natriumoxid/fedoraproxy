fedoraproxy
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
-getDatastream
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


### Installation


### Example 


### Version
1.0

### Last Change 
13.01.2014

### Authors
Franck Borel <franck.borel@ub.uni-freiburg.de>
Martin Helfer <martin.helfer@ub.uni-freiburg.de>

Based on the api of the software moodle-qti-cp-fedora written for the
University of Geneva by Laurent Opprecht (<laurent.opprecht@unige.ch>, <laurent@opprecht.info>)
and Nicolas Rod (<Nicolas.Rod@unige.ch>).

### Thanks
To Laurent Opprecht and Nicolas Rod for their fedora client api.


### Copying/License
GNU General Public License - http://www.gnu.org/copyleft/gpl.html

### Bugs
Not known
