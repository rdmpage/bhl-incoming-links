<?php

ini_set('memory_limit', '-1');

$basedir = 'specieswiki'; // depends on which wiki we are looking at

$files = scandir($basedir);

$pages = array();

$pcount = 0;
$lcount = 0;

foreach ($files as $filename)
{
	if (preg_match('/\.json$/', $filename))
	{	
		$json = file_get_contents($basedir . '/' . $filename);
		
		$obj = json_decode($json);
		
		foreach ($obj->query->exturlusage as $hit)
		{
			if (!isset($pages[$hit->pageid]))
			{
				$pages[$hit->pageid] = array();
			}
			if (!in_array($hit->url,$pages[$hit->pageid]))
			{
				$pages[$hit->pageid][] = $hit->url;
			}
			
		}
		
		
		if ($count++ > 30)
		{
			print_r($pages);
		
			echo count($pages);
			exit();
		}
		
	}
}
print_r($pages);

$pcount = count($pages);

foreach ($pages as $id => $links)
{
	$lcount += count($links);
}

echo $pcount . "\n";
echo $lcount . "\n";