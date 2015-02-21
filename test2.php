




 <!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<meta charset="UTF-8" />
	<meta http-equiv="content-language" content="en" />
	<title>MediaElement.js - HTML5 video player and audio player with Flash and Silverlight shims</title>

	<meta name="Copyright" content="Copyright (c) John Dyer" />
	<meta name="dc.language" content="en" />
	<meta name="geo.country" content="US" />

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

		<div id="top">
			<div id="header">
				<div id="logo">
					<h1><a href="/">MediaElement.js</a></h1>
					<!-- <p>HTML5 &lt;video&gt; and &lt;audio&gt; with H.264, FLV, WMV, MP3 on any browser.</p> -->
				</div>
				<div id="nav">
					<ul>
						<li><a href="/">Home</a></li>
						<li><a href="http://blog.mediaelementjs.com/">Blog</a></li>
						<li><a href="/#howitworks">How it Works</a></li>
						<li><a href="/#plugins">Plugins</a></li>
						<li><a href="/#installation">Installation</a></li>
						<li><a href="/#api">Options</a></li>
						<li><a href="/support/">Support</a></li>
						<!--<li><a href="#contact">Contact</a></li>-->
					</ul>
				</div>
			</div>
		</div>





<script src="/js/mep-feature-loop.js"></script>

<div class="container_12" class="plugin-header">
	<div class="grid_12">
		<h2>YouTube API Example</h2>
	</div>
</div>
<div class="clear"></div>

	
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
