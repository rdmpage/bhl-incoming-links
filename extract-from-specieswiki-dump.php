<?php

error_reporting(E_ALL);

// extract BHL links from a Wikispecies dump

// To get individual example
// https://species.wikimedia.org/wiki/Special:Export/Silvio_Shigueo_Nihei

$filename = 'Maurice_Pic.xml';

$filename = 'dump/specieswiki-20220701-pages-articles-multistream.xml';

$file_handle = fopen($filename, "r");

$debug = true;
//$debug = false;

$state = 0;
$page = '';
$title = '';
$refs = array();
$subject_type = '';

$timestamp = '';

$force = true;
//$force = false;

$count = 0;

echo "id\ttype\tbhl\n";

while (!feof($file_handle)) 
{
	$line = fgets($file_handle);
	
	// echo "$state | $line\n";
	
	switch ($state)
	{
		case 0:
			if (preg_match('/^\s+<page>/', $line))
			{
				$state = 1;
				$page = '';
				$subject_type = 'unknown';
				$timestamp = '';
				$title = '';
				//echo ".\n";
			}
			break;
			
		case 1:
			if (preg_match('/^\s+<\/page>/', $line))
			{
				// process
				$obj = new stdclass;
				
				$obj->id = str_replace(' ', '_', $title);
				$obj->id = str_replace('&amp;', '&', $obj->id);					
				$obj->title = $title;
				$obj->timestamp = $timestamp;
				$obj->type = $subject_type;
				
				$page = preg_replace('/\R/u', ' ', $page);	
				
				if (preg_match('/<text[^>]+>(.*)<\/text>/', $page, $m))
				{
					$obj->text = $m[1];
					$obj->text = html_entity_decode($obj->text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
					$obj->text = str_replace("&nbsp;", ' ', $obj->text);
					$obj->text = str_replace("&amp;", ' ', $obj->text);
				}
				
				//print_r($obj);
				
				// find links
				
				if (isset($obj->text))
				{
					// BHL page template					
					preg_match_all('/\{\{BHL\s*page\|(?<bhlpageid>\d+)\}\}/', $obj->text, $m);
					
					// print_r($m);					
					foreach ($m['bhlpageid'] as $bhl)
					{
						echo $obj->title . "\t/" . $obj->type . "\t" . "page/" . $bhl . "\n";
					}
					
					// BHL page URL
					preg_match_all('/biodiversitylibrary.org\/page\/(?<bhlpageid>[^\]|\s]+)[\]|\s]/u', $obj->text, $m);
					
					// print_r($m);					
					foreach ($m['bhlpageid'] as $bhl)
					{
						echo $obj->title . "\t" . $obj->type . "\t" . "page/" . $bhl . "\n";
					}
					
					// BHL item URL
					preg_match_all('/biodiversitylibrary.org\/item\/(?<itemid>[^\]|\s]+)[\]|\s]/', $obj->text, $m);
					
					// print_r($m);					
					foreach ($m['itemid'] as $bhl)
					{
						echo $obj->title . "\t" . $obj->type . "\t" . "item/" . $bhl . "\n";
					}

					// BHL title URL
					preg_match_all('/biodiversitylibrary.org\/title\/(?<titleid>[^\]|\s]+)[\]|\s]/', $obj->text, $m);
					
					// print_r($m);					
					foreach ($m['titleid'] as $bhl)
					{
						echo $obj->title . "\t" . $obj->type . "\t" . "title/" . $bhl . "\n";
					}					
					
				}
							
				$state = 0;
			}
			else
			{
				$page .= $line;
				
				if (preg_match('/^\s*<title>(?<title>.*)<\/title>/', $line, $m))
				{
					//print_r($m);
					$title = $m['title']; 
				}

				// <timestamp>2015-10-29T19:34:16Z</timestamp>

				if (preg_match('/^\s*<timestamp>(?<timestamp>.*)<\/timestamp>/', $line, $m))
				{
					//print_r($m);
					$timestamp = $m['timestamp']; 										
				}
				
				if (preg_match('/\[\[Category:Taxon authorities\]\]/', $line))
				{
					$subject_type = 'person';
				}
				
				if (preg_match('/\[\[Category:Reference templates\]\]/', $line))
				{
					$subject_type = 'reference';
				}
				
			}
			break;
				
		default:
			break;
			
	}
}

?>
