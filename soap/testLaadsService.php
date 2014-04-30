<?php
define ('LAADS_WSDL' , 'http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices?wsdl');

try {
  $client = new SoapClient(LAADS_WSDL);
  
  echo "<b>Calling listSatelliteInstruments():</b><br/>";
  $instrumentsResp = $client->listSatelliteInstruments();
  foreach($instrumentsResp as $return) {
    foreach($return as $instrument) {
      echo "Name = " . $instrument->name ." - Value = " .  $instrument->value . "<br />\n";
    }
  }
  
  echo "<br /><b>Calling the listProductsByInstrument(instrument)</b><br>";
  $productsInstrumentResp = $client->listProductsByInstrument("PM1M");
  foreach($productsInstrumentResp as $return) {
    foreach($return as $prod){
      echo "Product = $prod";
    }
  }
  
}
catch (SoapFault $sf) {
  echo "Sorry, there was a problem connecting to the service\n<br>";
  echo "The error msg was: (" . $sf->faultcode . ") - " . $sf->faultstring; 
}
catch (Exception $e) {
  $msg = $e->getMessage();
  echo "Unknown Exception: <b>$msg</b>";
}
?>