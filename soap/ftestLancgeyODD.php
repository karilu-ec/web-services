<?php
define ('LANCE_WSDL' , 'http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance?wsdl');

try {
  $client = new SoapClient(LANCE_WSDL, array('soap_version' => SOAP_1_1));
  $functions_array = $client->__getFunctions();
  
  //var_dump($functions_array);
  foreach ($functions_array as $function_info) {
    preg_match('/(\w*)\(/', $function_info, $matches);
    $api = $matches[1];
    preg_match('/(\(.*\))/', $function_info, $matches);
    echo "<p><b>$api</b>$matches[1]</p>";
  }
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