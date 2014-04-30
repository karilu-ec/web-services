<!DOCTYPE html>
<html>
<head>
    <title>Testing Google email monitor</title>
</head>
<body>
<?php
/********************************************/
/* Get the Auth token in order to call the
 * the email audit service.
 * The Auth token is retrieved making a request
 * to the ClientLogin authentication protocol
 * *******************************************/
function authenticate_admin($emailAdministrator, $passwordAdministrator) {
  $urlAuth = "https://www.google.com/accounts/ClientLogin";
  $headerAuth = array("Content-type: application/x-www-form-urlencoded");
  $authVars = array('Email' => $emailAdministrator,
                'Passwd' => $passwordAdministrator,
                'accountType'=>'HOSTED',
                'service' => 'apps',
                'source' => 'USNA-emailMonitor-1');
 
  $ch = curl_init($urlAuth);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headerAuth);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($authVars));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0");
  $response = curl_exec($ch);
  curl_close($ch);
  //Get the Auth Token
  $authTokenStr = strstr($response, "Auth=");
  if ($authTokenStr === false) {
    return false; //error with the POST request to the ClientLogin Authentication protocol.
  }
  $authTokenArray = explode ("=", $authTokenStr);
  $authToken = $authTokenArray[1];
  return $authToken;
}
 
 
//Set the Admin credentials
$emailAdministrator="ksimpson@usna.edu";
$passwordAdministrator = "Ro\$aParadi\$e08";

$userAgent = $_SERVER['HTTP_USER_AGENT'];

//Authenticate with clientLogin before calling the email audit API and get the Authentication token
$token = authenticate_admin($emailAdministrator, $passwordAdministrator);
if ($token === false) {
  print "There was an error authenticating the admin with Google ClientLogin";
  exit;
}

// Read the XML to send to EmailMonitor API
$string =<<<XML
<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>
   <apps:property name='destUserName' value='ksimpson'/>
   <apps:property name='beginDate' value='2012-11-01 00:00'/>
   <apps:property name='endDate' value='2012-11-28 23:20'/>
   <apps:property name='incomingEmailMonitorLevel' value='FULL_MESSAGE'/>
   <apps:property name='outgoingEmailMonitorLevel' value='HEADER_ONLY'/>
   <apps:property name='draftMonitorLevel' value='FULL_MESSAGE'/>
   <apps:property name='chatMonitorLevel' value='FULL_MESSAGE'/>
</atom:entry>
XML;
$xml = new SimpleXMLElement($string);
$xmldata = $xml->asXML();
$contentLength = strlen($xmldata);


//POST https://apps-apis.google.com/a/feeds/compliance/audit/mail/monitor/{domain name}/{source user name}
$url = 'https://apps-apis.google.com/a/feeds/compliance/audit/mail/monitor/usna.edu/ksimpson';
$ch = curl_init($url);
 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/atom+xml",
                                           "Accept: text/xml",
                                           "Cache-Control: no-cache",
                                           "Content-Length: $contentLength",
                                           "Authorization: GoogleLogin auth=$token"));
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
//curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0");
if (curl_errno($ch)) {
  echo "Error: " . curl_error($ch);
}
else {
  $response = curl_exec($ch);
  
  echo "Header sent was: " . "\n<br >";
  $info = curl_getinfo($ch, CURLINFO_HEADER_OUT);
  echo $info;
  
  curl_close($ch);
  //response from the monitor api.
  echo "\n<br>Response from email monitor:<br>";
  echo "<pre>";
  echo htmlspecialchars($response);
  echo "</pre>";
  var_dump($response);
  
}
?>
</body>
</html>   