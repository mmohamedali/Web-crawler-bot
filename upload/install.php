<?php
if(!empty($_POST['submit'])){
include './track/config.php';
$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD);  
		$query='CREATE TABLE IF NOT EXISTS `bot` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `visited_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `visited_ip` int(11) NOT NULL,
		  `visited_uri` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `visited_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `visited_by` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  `visited_referral` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `visited_page` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `visited_on` (`visited_on`),
		  KEY `visited_by` (`visited_by`),
		  KEY `visited_page` (`visited_page`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=35 ;';
		
		$sth = $dbh->query($query);		
		
		if($sth){
			$message= 'SUCCESS. Please, delete the install.php file, and connect to the admin dashboard <br><a href="./admin" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-user"></span> Login</a>'; 
		}else {
			$message= 'FAILED. Please, try again'; 
		}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Robots Crawlers Analytics * Install</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
        </style>
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</head>
<body>
	    <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Robots Crawlers Analytics * Install</div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                        <?php if(!empty($message)){ echo '<span style="color:red">'.$message.'</span>'; }else { ?> 
                        <form id="loginform" class="form-horizontal" role="form" method="post">                           
								<div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                      <input type="submit" class="btn btn-success" name="submit" value="Launch the install">

                                    </div>
                                </div>  
                            </form>     
							<?php } ?>
                        </div>                     
                    </div>  
        </div>
    </div>
</body>
</html>
