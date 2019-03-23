<?php

	function write_process($file, $pid)
    {
      $handle = file_get_contents(__DIR__.'/process/pid.json');
      if(!empty($handle))
      {
        $json = json_decode($handle, true);
      }
      $json[] = array(trim($file, "\n"),trim($pid, "\n"));
      file_put_contents(__DIR__.'/process/pid.json', json_encode($json));
    }

    function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    function writeInFile($data)
	{
		file_put_contents(__DIR__.'/result/result.txt', $data, FILE_APPEND | LOCK_EX);
	}

	function execCmd($domain)
	{
		$spf = '';
		$result = shell_exec("dig +short +time=5 +tries=1 -t txt $domain");
		$result = str_replace('" "', '', $result);
		$result = preg_replace('/(?<=\d)\s+(?=\d)/', '', $result);
		$result = explode("\n", $result);
		foreach ($result as $value)
		{
			if(stripos($value, 'v=spf1') !== false)
			{
				$spf = trim($value, '"');
				break;
			}
		}
		return !empty($spf) ? $spf : FALSE;
	}

	function deletePid($nameFile)
	{
		$jsonPid = file_get_contents(__DIR__."/process/pid.json");
        $json = json_decode($jsonPid, TRUE);

        foreach ($json as $key => $value) {
        	if($value[0] == $nameFile)
        	{
        		unset($json[$key]);
        		//echo 'yes';
        		break;
        	}

        }
        file_put_contents(__DIR__.'/process/pid.json', json_encode($json));

	}

	function killProcess($name, $pid)
	{
		shell_exec('kill -9 '.$pid);
		deletePid($name);
	}

	function ipv4_in_range($ip, $range)
	{

	    if (strpos($range, '/') !== false) {
	        list($range, $netmask) = explode('/', $range, 2);
	        if (strpos($netmask, '.') !== false) {
	            $netmask = str_replace('*', '0', $netmask);
	            $netmask_dec = ip2long($netmask);
	            return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
	        } else {
	            $x = explode('.', $range);
	            while(count($x)<4) $x[] = '0';
	            list($a,$b,$c,$d) = $x;
	            $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
	            $range_dec = ip2long($range);
	            $ip_dec = ip2long($ip);
	            $wildcard_dec = pow(2, (32-$netmask)) - 1;
	            $netmask_dec = ~ $wildcard_dec;

	            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
	        }
	    } else {
	        if (strpos($range, '*') !==false) {
	            $lower = str_replace('*', '0', $range);
	            $upper = str_replace('*', '255', $range);
	            $range = "$lower-$upper";
	        }

	        if (strpos($range, '-')!==false) {
	            list($lower, $upper) = explode('-', $range, 2);
	            $lower_dec = (float)sprintf("%u",ip2long($lower));
	            $upper_dec = (float)sprintf("%u",ip2long($upper));
	            $ip_dec = (float)sprintf("%u",ip2long($ip));
	            return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
	        }
	        return false;
	    }
	}

	function show_msg($string)
	{
	  echo strlen($string) != 0 ? $string : '';
	  ob_flush();
	  flush();
	}

	function checkInclude($spf)
	{
		preg_match_all('(include:[^\s]+)', $spf, $matches);
		$domains= array_map(function($v){
			return substr($v, 8);
		}, $matches[0]);
		return !empty($domains) ? $domains : false;
	}

 	$dom="";
	function checkSpfHtml($old, $domain)
	{
		GLOBAL $dom;
		if($old==0)
		{
		  $dom=$domain;
		}
		if($dom!=$domain)
		{
			$old=1;
		}
			$result = execCmd($domain);
			if(!empty($result))
			{
				show_msg('<tr>
	                  <th scope="row">'.$domain.'</th>
	                  <td>'.$result.'</td>
	                </tr>');

				$sub = checkInclude($result);


				if(!empty($sub))
				{
					foreach ($sub as $key => $value)
					{
						if($value == $domain)
						{
							continue;
						}
						else
						{
							if($dom!=$value)
							{
								checkSpfHtml(1, $value);
							}
							else
								break;
						}
					}
				}

			}

	}

	function checkSpfFile($domain)
	{
		$spf = execCmd($domain);
		if(!empty($spf))
		{
			writeInFile(json_encode(array('domain' => $domain, 'spf' => $spf))."\n");
			check($spf);
		}
	}

	function check($spf)
	{
		$include = checkInclude($spf);
		if(!empty($include)) {
			foreach ($include as $value) {
				$spff = execCmd($value);
				if(!empty($spff)) {
					writeInFile(json_encode(array('domain' => $value, 'spf' => $spff))."\n");
					$includee = checkInclude($spff);
					if(!empty($includee))	{
						foreach ($includee as  $valuee) {
							$spfff = execCmd($valuee);
							if(!empty($spfff)){
								writeInFile(json_encode(array('domain' => $valuee, 'spf' => $spfff))."\n");
							}
						}
					}
				}
			}
		}
	}
?>
