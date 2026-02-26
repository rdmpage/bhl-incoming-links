<?php

//----------------------------------------------------------------------------------------
function get($url, $format = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	if ($format != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: " . $format));	
	}
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------

$wiki_api_links = array(
	'enwiki' => 'https://en.wikipedia.org/w/api.php',
	'specieswiki' => 'https://species.wikipedia.org/w/api.php',
);

$limit = 500;

$base_parameters = array(
	'action' 	=> 'query',
	'euquery' 	=> 'biodiversitylibrary.org',
	'format' 	=> 'json',
	'list'		=> 'exturlusage',
	'eulimit'	=> $limit 
);



$done = false;

$eucontinue = 0;
$count = 0;

//$eucontinue = 325976;
//$count 		= 479678;

$wiki = 'specieswiki';

// This is where we store the output
$basedir  = dirname(__FILE__)  . "/" . $wiki;
if (!file_exists($basedir))
{
    $oldumask = umask(0); 
    mkdir($basedir, 0777);
    umask($oldumask);
}

// Call the API to get links
while (!$done)
{
	$parameters = $base_parameters;
	
	if ($eucontinue != 0)
	{
		$parameters['eucontinue'] = $eucontinue;
	}
	
	$query_url = $wiki_api_links[$wiki] . '?' . http_build_query($parameters);
	
	echo $query_url . "\n";
	
	$json = get($query_url);
	
	//echo $json;
	
	$output_filename = $basedir . '/' . $count . '.json';
	
	file_put_contents($output_filename, $json);
	
	$obj = json_decode($json);
	
	if ($obj->batchcomplete != "")
	{
		$done = true;
	}
	else
	{
		$eucontinue = $obj->continue->eucontinue;	
	}

	$count += count($obj->query->exturlusage);
	
	echo $count . "\n";
}

?>
