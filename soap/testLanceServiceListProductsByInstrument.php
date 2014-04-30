<?php
require_once "nusoap/lib/nusoap.php";
$client = new nusoap_client("http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance?wsdl" , true);
$error = $client->getError();
if ($error) {
  echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}
//do the call
$callResult = $client->call("listProductsByInstrument", array('instrument' => 'AM1M'));
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
    echo "List Products by Instrument<br />";
    echo "<pre>";
    print_r($callResult);
    /*foreach($callResult as $return) {
      foreach($return as $instrument_array) {
        echo "Name:". $instrument_array['name']."<br>";
        echo "Value:". $instrument_array['value']."<br>";
      }
    }*/
    echo "</pre>";
  }
}