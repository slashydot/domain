<?php

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
$cmd = parseArgs($_SERVER['argv']);
if(!isset($cmd['file'])) { usage(); exit; }
if(!file_exists($cmd['file'])) { usage(); echo "File ".$cmd['file']." containing bulk domain is not exist\r\n"; exit; }


$keys = array(
/** Go to https://developer.godaddy.com/ to register developer access **/
/** Add your godaddy API key (environment: Production) to these lines. The format is like: "access key:secret" **/
'eoMG1RR51EPd_SAKG2C1KJhzSaKjWET8cRV:XGuDLeRtb5iorSqqqmqKVA',
'eoMG1RR51uRx_9fFcKA5tfsjetgJgUH2YZC:5dNifMsJn9T49BUutkpeJ1',
'eoMG1RR6TY35_NeP1dZwfNAMhDvnoedMRLe:AePMuuYDZ4HQBeW5qLmSSe',
'eoMG1RR6UYQW_NLDoN5isNpuk4ctE8kzjwH:8LX5L76LdMLsx12wA9poLG',
'eoMG1RR6VYYa_6jNwKDBSzQb54GjkSNMVxU:9eAsDhnVnXVCSAHdcaa2vC'
);


checkKey();
if(count($keys) < 5){ echo "Error: Keys should be minimal 5 pairs"; exit; } 
$lines  = @file($cmd['file']);
$i = count($keys)-1;
$j =0;
$data = array();
banner();
echo "Start checking.......... \r\n\r\n";
foreach($lines  as $line ){
	if($j>$i) $j=0;
	$domain = trim($line);
	$message = "";	
	$govalue = "";
	$response = GDAppraisal($domain,$keys[$j]);
	if(isset($response['code']) && $response['code']==='TOO_MANY_REQUESTS') { echo "Error: Too many Request, add your Godaddy API key/secret in this code"; exit; } 
	if(isset($response['code']) && $response['code']==='UNABLE_TO_AUTHENTICATE') { echo "Error : Could not authenticate API key/secret ".$keys[$j].". Change the API key/secret\r\n"; continue; } 
	if(isset($response['message']) && $response['message'] !="") $message = trim($response['message']);
	if(isset($response['domain']) && $response['domain'] !="") $domain = $response['domain'];
	if(isset($response['govalue']) && $response['govalue'] !="") $govalue = $response['govalue'];
	if(isset($response['reasons']) && is_array($response['reasons'])) { foreach($response['reasons'] as $reason) { if(isset($reason['type']) &&  $reason['type']=='domain_history') { $message = "Domain History"; } } }
	array_push($data,array($domain,$govalue ,$message));
	if($govalue==="") echo $domain." => ".$message."\r\n"; else echo $domain." => $".$govalue." ".$message."\r\n";
	$j = $j+1;
}

$fileName = "file_".generateRandomString(24).".csv";
$fileCSV = toCSV($data, $fileName);
echo "\r\nFinish checking..........\r\n";
echo "Output is saved to ".$fileName;
 
 


function parseArgs($argv){
	array_shift($argv);
    $out = array();
    foreach ($argv as $arg){
        if (substr($arg,0,2) == '--'){
            $eqPos = strpos($arg,'=');
            if ($eqPos === false){
                $key = substr($arg,2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            } else {
                $key = substr($arg,2,$eqPos-2);
                $out[$key] = substr($arg,$eqPos+1);
            }
	} else if (substr($arg,0,1) == '-'){
            if (substr($arg,2,1) == '='){
                $key = substr($arg,1,1);
                $out[$key] = substr($arg,3);
            } else {
                $chars = str_split(substr($arg,1));
                foreach ($chars as $char){
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
	} else {
            $out[] = $arg;
        }
    }
    return $out;
}

 
 
function toCSV($rows,$output) { 
	$headers = array('Domain', 'Price', 'Status');
	$file = fopen($output, 'w');
	fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
	fputcsv($file, $headers, ";");
	foreach ($rows as $row) { fputcsv($file, $row, ";"); }
	fclose($file);  
}
 
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
} 
 
 function banner(){
	echo "-------------------------------------------------\r\n";
	echo "PHP Godaddy Bulk Apraisal coded by ucung\r\n";
	echo "-------------------------------------------------\r\n";
} 
 
 function usage(){
    banner();
	echo "\r\nUsage:\r\n";
	echo "Example: php ".$_SERVER['argv'][0]." --file=\"path/to/file_domain.txt\" \r\n";

}
 
 function checkKey(){
 global $keys;
 $domain = "test.com";
 foreach($keys  as $var => $value ){
 $response = GDAppraisal($domain, $value);
 if(isset($response['code']) && $response['code']==='UNABLE_TO_AUTHENTICATE') { unset($keys[$var]); continue; }
  }
  $keys = array_values($keys);

 }
  
function GDAppraisal($domain,$key) {
$apiURL = 'https://api.godaddy.com/v1/appraisal/'.$domain;
$headers = array(
    'Accept: application/json',
    'Authorization: sso-key '.$key,
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$server_output = curl_exec ($ch);
curl_close ($ch);
$output = json_decode($server_output, true);
return $output;
} 
	
	?>
