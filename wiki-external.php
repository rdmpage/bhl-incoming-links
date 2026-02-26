<?php

error_reporting(E_ALL);

ini_set('memory_limit', '-1');

/*
CREATE TABLE `externallinks` (
  `el_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `el_from` int(8) unsigned NOT NULL DEFAULT 0,
  `el_to_domain_index` varbinary(255) NOT NULL DEFAULT '',
  `el_to_path` blob DEFAULT NULL,
  PRIMARY KEY (`el_id`),
  KEY `el_to_domain_index_to_path` (`el_to_domain_index`,`el_to_path`(60)),
  KEY `el_from` (`el_from`)
) ENGINE=InnoDB AUTO_INCREMENT=1048744517 DEFAULT CHARSET=binary ROW_FORMAT=COMPRESSED;
*/

// how to link to a wikipedia page https://en.wikipedia.org/?curid=20000357

$filename = '/Volumes/Acer/enwikipedia-links/enwiki-latest-externallinks.sql';

$handle = fopen($filename, "r");

$chunkSize = 100000;
$overlap = 256;

$position = 0;
$prevTail = '';

$links = array();
$counter = 1;

while (!feof($handle)) {
    // Move to current position
    fseek($handle, $position);
    
    //echo "[$position]\n";

    // Read chunk
    $chunk = fread($handle, $chunkSize);

    // Prepend overlapping tail from previous chunk
    $combinedChunk = $prevTail . $chunk;
    
	/*
    // Process the combined chunk
    echo "---- Chunk starts at byte $position ----\n";
    echo substr($combinedChunk, 0, 200); // Just show first 200 chars for demo
    echo "\n----------------------------------------\n";
    */
    
	preg_match_all('/\((\d+,\d+[^)]+)\)/', $combinedChunk, $m);
	
	foreach ($m[1] as $row)
	{
		if (preg_match('/org.biodiversitylibrary/', $row))
		{
			$parts = explode(',', $row);
			
			if (count($parts) == 4)
			{			
				$el_from 			= $parts[1];
				$el_to_domain_index = $parts[2];
				$el_to_path 		= $parts[3];
				
				$el_to_domain_index = preg_replace('/^\'/', '', $el_to_domain_index);
				$el_to_domain_index = preg_replace('/\'$/', '', $el_to_domain_index);

				$el_to_path = preg_replace('/^\'/', '', $el_to_path);
				$el_to_path = preg_replace('/\'$/', '', $el_to_path);
			
				$links[$parts[0]] = [
					$el_from, $el_to_domain_index, $el_to_path
					];
			
			}
		}
	
	}

    // Save last 256 characters for overlap
    $prevTail = substr($chunk, -$overlap);

    // Advance position
    $position += ($chunkSize - $overlap);
	
}

fclose($handle);

echo "el_id\tel_from\tel_to_domain_index\tel_to_path\n";
foreach ($links as $el_id => $values)
{
	$row = array($el_id, $values[0], $values[1], $values[2]);
	
	echo join("\t", $row) . "\n";
}

#print_r($links);

?>

