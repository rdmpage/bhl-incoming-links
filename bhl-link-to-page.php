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
		
		echo "-- *** $errorText ***\n";
		return "";
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------

$filename = 'specieswiki.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			//print_r($obj);	
			
			echo "-- " . $obj->bhl . "\n";
			
			if (preg_match('/^(item|page|title)\/\d+$/', $obj->bhl))
			{
				// ok
				if (preg_match('/^page\/(\d+)$/', $obj->bhl, $m))
				{
					$sql = 'UPDATE specieswiki SET pageid=' . $m[1] . ' WHERE id="' . $obj->id . '" AND bhl="' . $obj->bhl . '";';
					echo $sql . "\n";
				}
				
			}
			else
			{
				$url = 'http://localhost/microcitation-parser/bhlurl.php?url=' . urlencode('http://www.biodiversitylibrary.org/' . $obj->bhl);
				$json = get($url);
				
				$result = json_decode($json);
				
				if ($result)
				{
					// print_r($result);
				
					if ($result->status == 200)
					{
						if (count($result->data->BHLPAGEID) > 0)
						{					
							$sql = 'UPDATE specieswiki SET pageid=' . $result->data->BHLPAGEID[0] . ' WHERE id="' . $obj->id . '" AND bhl="' . $obj->bhl . '";';
							echo $sql . "\n";
						}
					}
				}
				
				/*
				$rand = rand(1000000, 3000000);
				echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
				usleep($rand);
				*/
				
			}
		}
	}	
	$row_count++;
}

?>

