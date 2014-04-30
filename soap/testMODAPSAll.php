<?php
$client = new soapclient("http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices?wsdl");
var_dump($client->__getFunctions());

var_dump($client->__getTypes());