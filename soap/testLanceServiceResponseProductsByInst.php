<?php
define ('LANCE_WSDL' , 'http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance?wsdl');

try {
  $client = new SoapClient(LANCE_WSDL, array('trace' => 1));
  $productsResp = $client->listProductsByInstrument("ANC");
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