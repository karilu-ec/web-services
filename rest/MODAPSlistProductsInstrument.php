<?php
/*
 * Parse the MODAPS Web services using curl and simplexml
 */
libxml_use_internal_errors(true);

/*
 * function that formats the errors
 */
function display_xml_error($error, $xml)
{
  $return  = $xml[$error->line - 1] . "\n";
  $return .= str_repeat('-', $error->column) . "^\n";

  switch ($error->level) {
    case LIBXML_ERR_WARNING:
      $return .= "Warning $error->code: ";
      break;
    case LIBXML_ERR_ERROR:
      $return .= "Error $error->code: ";
      break;
    case LIBXML_ERR_FATAL:
      $return .= "Fatal Error $error->code: ";
      break;
  }

  $return .= trim($error->message) .
  "\n  Line: $error->line" .
  "\n  Column: $error->column";

  if ($error->file) {
    $return .= "\n  File: $error->file";
  }

  return "$return\n\n--------------------------------------------\n\n";
}


// The request parameters 
$instruments = array('AM1M', 'ANC', 'PM1M', 'AMPM', 'NPP');

// Get thep groups from the service
$groups = array();
foreach ($instruments as $instrument) {
    $requestGroups = "http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices/listProductGroups";
      //initialize the session
    $requestGroupsInstrument = $requestGroups . "?instrument=$instrument";
    $session = curl_init($requestGroupsInstrument);
      
    //Set curl options
    curl_setopt($session, CURLOPT_HEADER, true);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      
    //Make the request
    $response = curl_exec($session);
      
    //Close the curl session
    curl_close($session);
    //Get the XML from the response, bypassing the header
    if (!($xml = strstr($response, '<'))) {
       $xml = null; 
    } 
    //Output the data using SimpleXML which returns the data as a SimpleXML object
    $respObj = simplexml_load_string($xml);
    if ($respObj === false) {
      die ('Parsing failed');
    }
    $count = count($respObj->return);
    for ($i=0; $i<$count; $i++) {
      $type = (string)$respObj->return[$i]->attributes("mws", true)->type;
      $r = $respObj->return[$i]->children('http://modapsws.gsfc.nasa.gov/xsd');
      switch($type) {
        case 'mws:NameValuePair':
          $groups[$instrument][(string) $r->name] = (string) $r->value ;
          break;
        case 'mws:IntValuePair':
          $groups[$instrument][(int) $r->id] = (string) $r->value ;
          break;
        default:
          $groups[$instrument] = (string) $respObj->return[$i];
          break;
      }
    }
}

$request_array= array();
foreach ($instruments as $instrument) {
  //Searching for list products by group
  $request = "http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices/listProductsByInstrument";
  // urlencode and concatenate the GET arguments 
  $args = 'instrument='.urlencode($instrument);
  
  if (isset($groups[$instrument])) {
    foreach ($groups[$instrument] as $group => $group_value) {
      $args2= '&group='. urlencode($group);
      $thisrequest=  $request .'?' . $args . $args2;
      $request_array [$instrument][$group] = $thisrequest;
    }
  }
  else{
    $thisrequest =  $request .'?' . $args;
    $request_array [$instrument][] = $thisrequest;
  }
}
foreach($request_array as $request_key => $request_value) {
  foreach($request_value as $request_group => $request) {
  //initialize the session
  $session = curl_init($request);
  
  //Set curl options
  curl_setopt($session, CURLOPT_HEADER, true);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  
  //Make the request
  $response = curl_exec($session);
  
  //Close the curl session
  curl_close($session);
  
  //Check the HTTP status code
  $status_code = array();
  preg_match('/\d\d\d/', $response, $status_code);
  
  switch ($status_code[0]) {
    case 200;
      //Success
      break;
    case 503:
      die("Your call to the web service failed and returned an HTTP status of 503. That means: Service unavailable. ");
      break;
    case 403:
      die("Your call to the web service failed and returned an HTTP status of 403. That means: Forbidden. You don't have the permissions to access the resource");
      break;
    case 400:
      die("Your call to the web service failed and returned an HTTP status of 400. That means: Bad request. Maybe the parameters were not sent as expected. Check the XML response for the exact error");
      break;
    default:
      die("Your call to the web service failed and returned an unexpected status of $status_code[0]");
  }
  
  //Get the XML from the response, bypassing the header
  if (!($xml = strstr($response, '<'))) {
    $xml = null;
  }
  
  //Output the data using SimpleXML which returns the data as a SimpleXML object
  $respObj = simplexml_load_string($xml);
  
  if ($respObj === false) {
    die ('Parsing failed');
  }
  
  //get children through namespaces
  echo "<b>listProducts $request_key </b>". (!is_numeric($request_group) ? $groups[$request_key][$request_group] . '- '. $request_group : '')."<br />";
  
  //$resp = $respObj->children('http://laads.modapsws.gsfc.nasa.gov');
  //foreach($resp as $returnmy) {
    $count = count($respObj->return);
    echo "Total of products: $count <br>";
    for ($i=0; $i<$count; $i++) {
      $type = (string)$respObj->return[$i]->attributes("mws", true)->type;
      $r = $respObj->return[$i]->children('http://modapsws.gsfc.nasa.gov/xsd');
      switch($type) {
        case 'mws:NameValuePair':
          echo "name=" . (string) $r->name . " - ";
          echo "value=" . (string) $r->value . "<br />";
          break;
        case 'mws:IntValuePair':
          echo "id=" . (int) $r->id . " - ";
          echo "value=" .(string) $r->value . "<br />";
          break;
        default:
          if($count >1) {
            echo "return: " . (string) $respObj->return[$i] . "<br>";
          }
          else {
            echo "return: " . (string) $respObj->return. "<br>";
          }
          break;
      }
  }
  }
}