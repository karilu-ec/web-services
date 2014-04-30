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


//Searching for list Satellite Instruments
$request = "http://modwebsrv.modaps.eosdis.nasa.gov/axis2/services/MODAPSservices/listSatelliteInstruments";
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

//register namespace
$respObj->registerXPathNamespace('ns', 'http://laads.modapsws.gsfc.nasa.gov');
$respObj->registerXPathNamespace('ax21', 'http://datatypes.laads.modapsws.gsfc.nasa.gov/xsd');
$respObj->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$return = $respObj->xpath('//ns:return');
$name = $respObj->xpath('//ax21:name');
$value = $respObj->xpath('//ax21:value');
$count = count($name);

echo "<b>listSatelliteInstruments</b><br />";

for ($i =0; $i < $count; $i++) {
  echo $name[$i] . "= ". $value[$i] . "<br>"; 
}
