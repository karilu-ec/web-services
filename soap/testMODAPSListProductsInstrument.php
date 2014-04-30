<?php
require_once "nusoap/lib/nusoap.php";
$client = new nusoap_client("http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices?wsdl" , true);
$error = $client->getError();
if ($error) {
  echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}

$instrument = array( 'instrument' =>  'AM1M');//SOAP::Data->name('instrument')->value('AM1M')->type('string'); 
//do the call
$callResult = $client->call("listProductsByInstrument", array('parameter' => $instrument));
//check fault
if ($client->fault) {
  echo "<strong>Fault:</stron>";
  print_r($callResult);
}
else {
  //check error
  $err = $client->getError();
  if ($err) {
    echo "<strong>Error:</strong>".$err;
  }
  else {
    echo "List products by Instruments<br />";
    echo "<pre>";
    print_r($callResult);
    foreach($callResult as $return) {
      foreach($return as $instrument_array) {
          echo "Name:". $instrument_array['name']."<br>";
          echo "Value:". $instrument_array['value']."<br>";
      }
    }
    echo "</pre>";
  }
}
// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
// Display the debug messages
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';