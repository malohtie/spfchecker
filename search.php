<!DOCTYPE html>
<html lang="en">
<head>
  <title>SPF Search</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="./favicon.png">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid" style="margin-top: 30px">
  <div class="row">
    <div class="col-sm-10 offset-sm-1">

		<?php
			include_once 'functions.php';
			if(!empty($_GET['ip']))
			{
				ini_set("memory_limit", "-1");
				ini_set('max_execution_time', "0");
				$ip = $_GET['ip'];
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE))
				{
					show_msg('<div class="alert alert-success alert-dismissible fade show">
              					<button type="button" class="close" data-dismiss="alert">&times;</button>
              					<strong>Searching ...</strong>  '.$_GET['ip'].' please wait :) </div>');
					 show_msg('<div class="table-responsive"><table class="table table-hover table-striped table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Domain</th>
                            <th scope="col">SPF</th>
                            <th scope="col">Status</th>
                          </tr>
                        </thead><tbody>');
					$file = new SplFileObject(__DIR__."/result/result.txt");
					while (!$file->eof())
					{
					    // Echo one line from the file.
					    $line = $file->fgets();
					    $result = json_decode($line, TRUE);
					    $resultJson = $result['spf'];
					    preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?:\/\d{2})?/', $resultJson, $matches);
					    $resultJson = $matches[0];
					    foreach ($resultJson as $value)
					    {
                $resultCheck = ipv4_in_range($_GET['ip'], $value);
					    	if($resultCheck)
					    	{
					    		show_msg('<tr>
	                              <th scope="row">'.$result['domain'].'</th>
	                              <td>'.$result['spf'].'</td>
                                <td>'.$_GET['ip'].'--MATCH--'.$value.'</td>
	                            </tr>');
					    		break;
					    	}
					    }
					}

					show_msg('</tbody>
                            </table></div>');

					show_msg('<div class="alert alert-info alert-dismissible fade show">
              					<button type="button" class="close" data-dismiss="alert">&times;</button>
              					<strong>ENDED</strong> search Ended </div>');
				}
				else
				{
					show_msg('<div class="alert alert-danger alert-dismissible fade show">
              					<button type="button" class="close" data-dismiss="alert">&times;</button>
              					<strong>Oops!</strong>  '.$_GET['ip'].' IS NOT A VAlID IP </div>');
				}
			}
		?>
	</div>
 	</div>
 </div>
</body>
</html>
