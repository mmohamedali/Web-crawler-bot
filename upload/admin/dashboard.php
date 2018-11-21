<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
	
    <title>Robots Crawlers Analytics *ADMIN</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<link rel='stylesheet' id='style-css'  href='./resources/style.css' type='text/css' media='all' />
	
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="blog-masthead">
      <div class="container">
        <nav class="blog-nav">
          <a class="blog-nav-item active" href="#home">Home</a>
          <a class="blog-nav-item" href="#hourly">Visits - Hourly</a>
          <a class="blog-nav-item" href="#daily">Visits - Daily</a>
          <a class="blog-nav-item" href="#details">Visit Details</a>
          <a class="blog-nav-item" href="#pages">Most Crawled Pages</a> 
		  <a class="blog-nav-item" href="#spread">Crawl Spread</a>
		  <a class="blog-nav-item" href="#times">Peak Crawl Times</a>
		  <a class="blog-nav-item" href="#integration">*Integration</a>
        </nav>
      </div>
    </div>
<a name="home"></a>
    <div class="container">

      <div class="blog-header">
        <h1 class="blog-title">Robots Crawlers Analytics : Dashboard</h1>
        <p class="lead blog-description">Track & analyse search engine spiders | Gain a better understanding of the behaviour of robots</p>
      </div>

      <div class="row">

        <div class="col-md-8">
		  <div class="panel panel-primary"><a name="hourly"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Robot Visits - Hourly</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_widget_function(); ?>
			  </div>
		  </div>
		  <div class="panel panel-primary"><a name="daily"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Robot Visits - Daily</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_plain_graph_function(); ?>
			  </div>
		  </div>

        </div><!-- /.blog-main -->

        <div class="col-md-4">	
		  <div class="panel panel-primary"><a name="pages"></a> 
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Most 10 Crawled Pages</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_widget_top_pages(); ?>
			  </div>
		  </div>
		  <div class="panel panel-primary"><a name="spread"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Crawl Spread</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_widget_top_bots(); ?>
			  </div>
		  </div>
		  <div class="panel panel-primary"><a name="times"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Peak Crawl Times</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_widget_active_times(); ?>
			  </div>
		  </div>
        </div><!-- /.blog-sidebar -->

      </div><!-- /.row -->
	  
	  <div class="row">
        <div class="col-md-12">
			<div class="panel panel-primary"><a name="details"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Visit Details</h2></div>
			  <div class="panel-body">
				<?php bota_dashboard_latest_function(); ?>
			  </div>
		  </div>
		</div>
	  </div>
	  
	  <div class="row">
        <div class="col-md-12">
			<div class="panel panel-primary"><a name="integration"></a>
				<div class="panel-heading"><h2 class="blog-post-title" style="color:white">Integration</h2></div>
			  <div class="panel-body">
					<p >To integrate this tracking system into other parts of your site, you can simply copy and paste this small snippet into your HTML.</p>
							
                            <pre>&lt;img src="<?php 
							$url = $_SERVER["REQUEST_URI"];
							$url = str_replace("/admin/", "/track/", $url);
							echo $url;  ?>" /&gt;</pre>

				<br><i>Read the documentation file for more details</i>
			  </div>
		  </div>
		</div>
	  </div>
    </div><!-- /.container -->

    <div class="blog-footer">
      <p>
        Robots Crawlers Analytics Script | <a href="#">Back to top</a>
      </p>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script type='text/javascript' src='./resources/jquery.flots.datatables.min.js'></script>
	<script type='text/javascript' src='./resources/jquery.flot.pie.js'></script>
  </body>
</html>
