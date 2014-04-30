<?php
define ('LANCE_WSDL' , 'http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance?wsdl');

try {
  $client = new SoapClient(LANCE_WSDL, array('soap_version' => SOAP_1_1));
  
  echo "<b>Calling listSatelliteInstruments():</b><br/>";
  $instrumentsResp = $client->listSatelliteInstruments();
  foreach($instrumentsResp as $return) {
    foreach($return as $instrument) {
      echo "Name = " . $instrument->name ." - Value = " .  $instrument->value . "<br />\n";
    }
  }
  
  echo "<br /><b>Calling listCollections():</b><br/>";
  $collectionsResp = $client->listCollections();
  foreach($collectionsResp as $return) {
    foreach($return as $collection) {
      echo "Id= " . $collection->id . " - Value= " . $collection->value . "<br />\n";
    }
  }
  
  echo "<br /><b>Calling listProducts():</b><br/>";
  $productsResp = $client->listProducts();
  foreach($productsResp as $return) {
    foreach($return as $product) {
      echo "Name= " . $product->name . " - Value= " . $product->value . "<br /> \n";
    }
  }
  
  echo "<br /><b>Calling the listProductsByInstrument(PM1M)</b><br>";
  $parameters = new stdClass();
  $parameters->instrument->name = "PM1M"; 
  
  $productsInstrumentResp = $client->listProductsByInstrument($parameters);
  
  echo "<br /><b>Calling the listProductsByInstrument(ANC)</b><br>";
  $parameters = new stdClass();
  $parameters->instrument->name = "ANC"; 
  
  $productsInstrumentResp = $client->listProductsByInstrument($parameters);

  echo "<br /><b>Calling the listVersion()</b><br>";
  $versionResp = $client->getVersion();
  echo "Version is ".$versionResp->return;
  
  echo "<br /><br /><b>Calling the getMaskTypes()</b><br>";
  $maskType = $client->getMaskTypes();
  echo "Mask Types " . $maskType->return;
  
  echo "<br /><br /><b>Calling the getMaxSearchResults()</b><br>";
  $max = $client->getMaxSearchResults();
  echo "Max Search results " . $max->return;
  
  
  
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