<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<meta charset="UTF-8" />
	<meta http-equiv="content-language" content="en" />

	<!-- mediaelement -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script> 
	<script src="/js/build/mediaelement-and-player.min.js"></script>
	<link  href="/js/build/mediaelementplayer.min.css" rel="Stylesheet" />
	<link  href="/js/build/mejs-skins.css" rel="Stylesheet" />

	
	<meta name="viewport" content="width=1000, user-scalable=yes" />
	<meta name="description" content="HTML5 video player with Flash and Silverlight fallbacks making it compatible with any browser." />
	<meta name="keywords" content="HTML5, video, H.264, Javascript, media, mp3, mp4" />
</head>
<body>
	<div id="container">


	
<div class="plugin-example-container">

	<div class="plugin-example">

		<video id="player1" width="640" height="360" controls="control" preload="none">		
			<source src="http://www.youtube.com/watch?v=Lt6CUH9bVzI" type="video/youtube" />
		</video>	
		
	</div>

</div>

<script>
jQuery(document).ready(function($) {

	// declare object for video
	var player = new MediaElementPlayer('#player1');

});
</script>

<div id="separator"></div>

	</div><!-- /#container -->


</body>
</html>
