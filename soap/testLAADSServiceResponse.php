<?php
define ('LAADS_WSDL' , 'http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices?wsdl');

try {
  $client = new SoapClient(LAADS_WSDL, array('trace' => 1));
  $productsInstrumentResp = $client->listProductsByInstrument(array("name"=>"AM1M") );
  print "<pre>\n";
  
  print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
  
  print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
  
  print "</pre>";
}
catch (SoapFault $sf) {
echo "Sorry, there was a problem connecting to the service";
echo "The error msg was: (" . $sf->faultcode . ")" . $sf->faultstring;
}
catch (Exception $e) {
$msg = $e->getMessage();
echo "Unknown Exception: <b>$msg</b>";
}