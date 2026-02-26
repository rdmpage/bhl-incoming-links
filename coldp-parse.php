<?php

//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------

$filename = 'afromoths/nameusage.csv';
$filename = '2eb31b95-a8e3-4aa5-9f22-67c45a3723f9/NameUsage.tsv';
$filename = '2eb31b95-a8e3-4aa5-9f22-67c45a3723f9/Reference.tsv';

echo "id\tlabel\turl\tpageid\n";

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	if (preg_match('/csv$/', $filename))
	{
		$row = fgetcsv(
			$file_handle, 
			0, 
			translate_quoted(','),
			translate_quoted('"') 
			);
	}
	else
	{
		$row = fgetcsv(
			$file_handle, 
			0, 
			"\t" 
			);	
	}
	
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
			
			$id = '';
			
			if (isset($obj->{'col:ID'}))
			{
				$id = $obj->{'col:ID'};
			}
			if (isset($obj->ID))
			{
				$id = $obj->ID;
			}	
			
			$label = '';
			if (isset($obj->scientificName))
			{
				$label = $obj->scientificName;	
			}	
			
			$url = '';
			
			if (isset($obj->{'col:link'}))
			{
				$url = $obj->{'col:link'};
			}

			if (isset($obj->namePublishedInPageLink))
			{
				$url = $obj->namePublishedInPageLink;
			}
			
			if ($url != '')
			{
				if (preg_match('/biodiversitylibrary.org/', $url))
				{		
					// fix for Afromoths
					$url = preg_replace('/\x0A.*$/', '', $url);
					$url = preg_replace('/(\d+)http.*$/', '$1', $url);
				
					$output = array(
						$id,
						$label,
						$url,
						""
					);
					echo join("\t", $output). "\n";
				}
				
			}

		}
	}	
	$row_count++;
}


?>

