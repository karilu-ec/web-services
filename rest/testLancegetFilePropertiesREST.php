<?php
/*
 * Parse the LANCE Web services using curl and simplexml 
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


//Searching for getFileProperties
$request = "http://lance2.modaps.eosdis.nasa.gov/axis2/services/MWSLance/getFileProperties?fileIds=77888666";
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

echo "<pre>$xml</pre>";

//Output the data using SimpleXML which returns the data as a SimpleXML object
//$xml = '<mws:getFilePropertiesResponse xmlns:mws="http://modapsws.gsfc.nasa.gov/xsd"><return mws:type="mws:FileProperties"><mws:checksum>366559448</mws:checksum><mws:fileId>77888666</mws:fileId><mws:fileName>MYD04_L2.A2012072.1810.051.2012072203353.NRT.hdf</mws:fileName><mws:fileSizeBytes>2030970</mws:fileSizeBytes><mws:fileType>MYD04_L2</mws:fileType><mws:ingestTime>2012-03-12 20:33:57.407049</mws:ingestTime><mws:online>true</mws:online><mws:startTime>2012-03-12 18:10:00.0</mws:startTime></return></mws:getFilePropertiesResponse>';
$respObj = simplexml_load_string($xml);


if (!$respObj) {
 // die ('Parsing failed');
 $errors = libxml_get_errors();
 foreach($errors as $error) {
   echo display_xml_error($error, $xml);
 }
 libxml_clear_errors();
}


//Collect the return array
echo "<p><b>Making a REST call to getFileProperties where the fileid is 77888666:<br />";
//print_r($respObj);
$countReturns = $respObj->return->count();
for($i=0; $i<$countReturns; $i++){
  echo "File Properties=" . $respObj->return[$i] . "<br />";
}

