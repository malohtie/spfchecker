<?php  include_once 'functions.php'; ?>
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
  <style>
    /* Show it is fixed to the top */
    body {
      min-height: 75rem;
      padding-top: 4.5rem;
    }
  .fakeimg {
    height: 200px;
    background: #aaa;
  }

  </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="./index.php">SPF SCAN</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">TOTAL VALID DOMAINS SCANNED (have spf) : 
              <?php
                $path = __DIR__.'/result/result.txt';
                echo shell_exec("wc -l < $path");
              ?>
              </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="?action=removeduplicate">REMOVE DUPLICATE </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./check.php">CHECK</a>
          </li>
        </ul>
        <form class="form-inline mt-2 mt-md-0" method="get" action="./search.php" target="_blank">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search" name="ip">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

<div class="container-fluid" style="margin-top: 30px">
  <div class="row">
    <div class="col-sm-8 offset-sm-2">
        <form action = "index.php" method = "POST" enctype = "multipart/form-data">
          <div class="input-group mb-3">
            <div class="custom-file">
              <input type="file" accept=".txt" class="custom-file-input" id="inputGroupFile02" name="file">
              <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
            </div>
            <div class="input-group-append">
              <button type="submit" class="btn btn-success" name="upload">Upload</span>
            </div>
          </div>
        </form>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-8 offset-sm-2">
      <?php
       
        if(!empty($_GET['msg']))
        {
          echo base64url_decode($_GET['msg']);
        }

        if(isset($_POST['upload']))
        {
          $target_dir = "./files/";
          $target_file = $target_dir . basename(preg_replace('/\s+/', '_',$_FILES["file"]["name"]));
          $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
          if (empty($_FILES["file"]["name"])) 
          {
            $msg = base64url_encode('<div class="alert alert-info alert-dismissible fade show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Info!</strong> Please select a TXT file.
                  </div>');
            echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
          }
          if (file_exists($target_file)) 
          {
            $msg = base64url_encode('<div class="alert alert-danger alert-dismissible fade show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Oops!</strong> This file existe already !
                  </div>');
            echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
            exit;
          }
          if ($fileType != 'txt') 
          {
            $msg = base64url_encode('<div class="alert alert-danger alert-dismissible fade show">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Oops!</strong> Only TXT file Allowed !
                  </div>');
            echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
          }
          if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $msg = base64url_encode('<div class="alert alert-success alert-dismissible fade show">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Success!</strong> ' . basename( $_FILES["file"]["name"]). " has been uploaded Processing domains :)</div>");

            $process = __DIR__.'/process.php';
            $file = __DIR__.'/files/'.$_FILES["file"]["name"];
            $cmd = "nohup /usr/bin/php {$process} {$file} > /dev/null & echo $!";
            $r = shell_exec($cmd);
            write_process(basename(preg_replace('/\s+/', '_',$file)), $r);
            echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
          } 
          else 
          {
            $msg = base64url_encode('<div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Oops!</strong> Sorry, there was an error uploading your file. !
                </div>');
            echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
          }

        }
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="card" style="margin-top: 25px">
        <div class="card-header">
          Uploaded Files
        </div>
        <div class="card-body">
          <?php 
            $files = glob("./files/*.txt");
            if(!empty($files))
            {
              foreach ($files as $file) {
                $nameFile = basename($file);
                echo "<p class=\"card-text\">$nameFile &nbsp; <a href=\"?action=delete&file=$nameFile\" class=\"btn btn-outline-danger btn-sm\">DELETE</a></p>";
              }
            }
            else
            {
              echo '<h5>No Uploaded Files</h5>';
            }

          ?>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card" style="margin-top: 25px">
        <div class="card-header">
          Runing Process <a href = "./index.php" class='btn btn-info btn-sm'>REFRESH</a>
        </div>
        <div class="card-body">
          <?php 
            if(file_exists('./process/pid.json'))
            {
              $jsonPid = file_get_contents(__DIR__."/process/pid.json");
              $json = json_decode($jsonPid, TRUE);
              if(!empty($json))
              {
                $table = '<div class="table-responsive"><table class="table table-hover table-striped table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">File</th>
                            <th scope="col">Pid</th>
                            <th scope="col">Kill</th>
                          </tr>
                        </thead><tbody>';
                foreach ($json as $pid) 
                {
                  $table .= '<tr>
                              <th scope="row">'.$pid[0].'</th>
                              <td>'.$pid[1].'</td>
                              <td><a href=?action=kill&file='.$pid[0].'&pid='.$pid[1].' class="btn btn-outline-danger btn-sm">KILL</a></td>
                            </tr>';
                }
                $table .= '</tbody>
                            </table></div>';
                echo $table;
              }
              else
              {
                echo '<h5>No Running Process</h5>';
              }
            }
            ?>
        </div>
      </div>
    </div>
  </div>
</div>
  <script>
    $('#inputGroupFile02').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    $('.close').click(function(e){
      e.preventDefault();
      window.location = './index.php';
    });
</script>
</body>
</html>
<?php 
  if(!empty($_GET['action']))
  {
    if($_GET['action'] == 'delete' && !empty($_GET['file']))
    {
      if(file_exists(__DIR__.'/files/'.$_GET['file']))
      {
        shell_exec('rm -rf '.__DIR__.'/files/'.$_GET['file']);
        echo '<script>window.location.href = "./index.php";</script>';
      }
    }
    //kill pid
    if($_GET['action'] == 'kill' && !empty($_GET['file']) && !empty($_GET['pid']))
    {
        killProcess($_GET['file'], $_GET['pid']);
      
        $msg = base64url_encode('<div class="alert alert-info alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>STOPPED!</strong> Process STOPPED !</div>');
        echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
    }

    if($_GET['action'] == 'removeduplicate')
    {
        $path = __DIR__.'/result/result.txt';
        shell_exec("sort -u -o $path $path");
        $msg = base64url_encode('<div class="alert alert-info alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>DONE!</strong> DUPLICATED REMOVED !</div>');
        echo '<script>window.location.href = "./index.php?msg='.$msg.'";</script>';
    }
  }

?>