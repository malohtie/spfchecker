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
        <form action = "check.php" method = "POST" enctype = "multipart/form-data">
          <div class="form-group">
            <label for="domains">Domains:</label>
            <textarea class="form-control" rows="5" id="domains" name="domains"></textarea>

          </div>
           <input type="submit" name="action" value="check" class="btn btn-success" />
        </form>
    </div>
    <div class="col-sm-10 offset-sm-1">
      <?php 
        if(!empty($_POST['action']) && !empty($_POST['domains']))
        {
          if($_POST['action'] == 'check')
          {
             show_msg('<div class="" style="margin-top: 20px;"><table class="table table-hover table-striped table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Domain</th>
                            <th scope="col">SPF</th>
                          </tr>
                        </thead><tbody>');
             
             $domains = explode("\n", $_POST['domains']);
             $domains = array_filter($domains, 'strlen');
             foreach ($domains as $key => $value) 
             {
             	checkSpfHtml(0, $value);
             }
             show_msg('</tbody></table>');            
          }
        }
      ?>
    </div>
  </div>
</div>
  <script>
    $('.close').click(function(e){
      e.preventDefault();
      window.location = './index.php';
    });
</script>
</body>
</html>
