<?php
$options = array('trace' => true);
$client = new soapclient("http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices?wsdl" , $options);
try{
  
} catch (SoapFault $fault){
  trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}\n" .
"faultstring: {$fault->faultstring})", E_USER_ERROR);
}