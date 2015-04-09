<script src="/js/aw-party-player_server.js"></script>
<script src="/js/build/mediaelement-and-player.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/build/mediaelementplayer.css" type="text/css">
<script>
	<?php

        //Si l'url ne contient pas les infos necessaire on relance l'accueil step2
        if($sessid == ''){ ?>
	window.location.href = <?php echo $server_base_url; ?>+rand_sessid;
	<?php }
		else{ 
			echo "sessid = '".$sessid."';loadPlaylistFromServer();";
			echo "if(typeof username === 'undefined' || username == ''){setUsername(serverPrefix+'".$sessid."');}";
		}
	 ?>

	setInterval(function() {
		// Do something every 5 seconds
		loadPlaylistFromServer();
	}, 5000);

	//variable uniquement sur le serveur
	playerIsLoaded = false;

	//variable pour la tentative de crossfading
	preloadingPlayerIsLoaded = false;
	preloadingInterval = false;
	crossfadeTime = 5;

	autoPlay = true;
	lastPlayedId = 0;
	bootboxInstanceLoadNextTrack = null;

</script>
<div class='row first_row'>

	<div id='message'></div>

	<div class='col-xs-12 col-sm-8' id='colonne_gauche'>
		<script>
			#player-wrapper {
				position: relative;
				padding-bottom: 56.25%; /* 16:9 */
				padding-top: 25px;
				height: 0;
			}
			#player-wrapper iframe {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
		</script>
		<div class='well placeholders'>
			<h2 id='main-title'>C'est prêt, Ajoutez des musiques!</h2>
			<div id='player-wrapper'>
				Laissez cette page ouverte pour écouter la musique.<br/>
				Ajouter des musiques depuis vos smartphones<br/>
				<img src='/img/phone.svg' width='100px' /><br/>
				Flashez le QRCode  ==><br/>
				<br/>
				ou<br/>
				<br/>
				Allez sur <a target="_blank" href="<?php echo $session_url; ?>"><b><?php echo $session_url; ?></b></a><br/>
			</div>
			<div id='vote-area-ph'></div>
		</div>

	</div>

	<!-- Colonne de droite -->
	<div class='col-xs-12 col-sm-4' id='colonne_droite'>
		<div class="panel panel-warning" id='share_panel'>
			<div class="panel-heading">
				<h3 class="panel-title" style="display:inline">JukeBox <b><?php echo $sessid ?></b></h3>
				<span onclick='$("#share_panel").hide();' style='float:right;cursor:pointer;'>X</span>
			</div>
			<div class="panel-body placeholders">
				<img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=<?php echo urlencode($session_url); ?>&choe=UTF-8" /><br/>
			</div>
		</div>

		<?php include_once('_playlist.php'); ?>
	</div>