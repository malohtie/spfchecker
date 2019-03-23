<?php
	include_once 'functions.php';
	if(!empty($argv[1]))
	{
		ini_set("memory_limit", "-1");
		ini_set('max_execution_time', "0");
		$file = $argv[1];
		if(file_exists($file))
		{
			$file = new SplFileObject($file);
			while (!$file->eof())
			{
			   	$line = trim($file->fgets());
					echo $line."\n";
		   		checkSpfFile($line);
			}
			fclose($handle);
			deletePid(basename($argv[1]));
		}
	}
?>
