<?php
define ('LANCE_WSDL' , 'http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance?wsdl');

try {
  $client = new SoapClient(LANCE_WSDL);
  $types_array = $client->__getTypes();
  var_dump($types_array);
}
catch (SoapFault $sf) {
  echo "Sorry, there was a problem connecting to the service";
  echo "The error msg was: (" . $sf->faultcode . ")" . $sf->faultstring; 
}
catch (Exception $e) {
  $msg = $e->getMessage();
  echo "Unknown Exception: <b>$msg</b>";
}
?>