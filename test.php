<!DOCTYPE html>
<html lang='fr'>
<?php
    
    $rand_sessid = strtoupper(substr(md5(rand()), 0, 3));
    $sessid = '';
    if(isset($_REQUEST['sessid']) ){
        $sessid = $_REQUEST['sessid'];
    }
    
    $base_url = "http://".$_SERVER['SERVER_NAME'];
    $session_url =  $base_url."?mode=client&sessid=".$sessid;
    $serveur_url = "server.php";
    
    $marge_header = '70';
    $boostrapversion = "3.3.2";
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Party Player">
    <meta name="author" content="WEBER Antoine">
    <script src="/js/build/jquery.js" type="text/javascript"></script>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/<?php echo $boostrapversion; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/<?php echo $boostrapversion; ?>/css/bootstrap-theme.min.css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/<?php echo $boostrapversion; ?>/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>

	<script src="/js/build/mediaelement-and-player.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/build/mediaelementplayer.css" type="text/css">
    <title>Party Player - le JukeBox collaboratif de vos soirées!</title>
</head>

<body>
    
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <!--<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>-->
          <a class="navbar-brand" href="/"><img src='/css/favicon.png' height='20px' /> Party Player <span style='color:red;font-style:italic;'>beta</span><span id="subtitle">&nbsp;- le JukeBox collaboratif de vos soirées!</span></a>
          <!--<div class='nav'><span>&nbsp; Transformer votre PC en Jukebox!<span></div>-->
        </div>
        
        <!--<div id="nav-main" class="navbar-collapse collapse">
            <a class="navbar-brand" id='username' onclick='changeUsername();' style='float:right'>UserName</a>     
        </div>-->
        
      </div>
    </div>
    
    
    <div class="container">
	    <div class="row">
		    <video id="audio-player" width="640"  height="360" >
			    <source type="video/youtube" src="https://www.youtube.com/embed/nfWlot6h_JM?list=PLvFYFNbi-IBFeP5ALr50hoOmKiYRMvzUq" />
			</video>
	    </div>
    </div>
    
    <script>
        
        mediaPlayer = new MediaElementPlayer("#audio-player");
        
      </script>   
        </body>
</html>