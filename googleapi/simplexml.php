<?php
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

echo $xml->asXml();
$xmldata = $xml->asXML();


$contentLength = strlen($xmldata);
echo "<br> Content-lenghth= ". $contentLength;
?>