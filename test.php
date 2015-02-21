<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<meta charset="UTF-8" />
	<meta http-equiv="content-language" content="en" />
	
	<meta name="viewport" content="width=1000, user-scalable=yes" />
	<meta name="description" content="HTML5 video player with Flash and Silverlight fallbacks making it compatible with any browser." />
	<meta name="keywords" content="HTML5, video, H.264, Javascript, media, mp3, mp4" />
    
    <script src="/js/build/jquery.js" type="text/javascript"></script>
	<script src="/js/build/mediaelement-and-player.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="/js/build/mediaelementplayer.css" type="text/css">
	<link rel="stylesheet" href="/js/build/mejs-skins.css" type="text/css">
</head>

<body>
    
    <div class="container">
	    <div class="row">
		    <video id="audio-player" width="640"  height="360" >
			    <source type="video/youtube" src="https://www.youtube.com/watch?v=nfWlot6h_JM&list=PLvFYFNbi-IBFeP5ALr50hoOmKiYRMvzUq" />
			</video>
	    </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
        	mediaPlayer = new MediaElementPlayer("#audio-player");
        }
		);        
      </script>   
        </body>
</html>