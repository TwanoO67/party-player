<script src="/js/aw-party-player_server.js"></script>
<script src="/js/build/mediaelement-and-player.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/build/mediaelementplayer.css" type="text/css">
<script>
<?php 
    
	//Si l'url ne contient pas les infos necessaire on relance l'accueil step2
	if($sessid == ''){ ?>
		window.location.href = '/?mode=server&sessid='+rand_sessid;
	<?php }
		else{
			echo "sessid = '".$sessid."';loadPlaylistFromServer();";
			echo "setUsername('Jukebox".$sessid.."');";
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
	    
	    <div class='well placeholders'>
	        <h2 id='main-title'>Votre JukeBox est prêt!</h2>
	        <div id='player-wrapper' width="50%" style="width:100%;position:relative;">
	            Demandez à vos amis d'ajouter des musique!<br/>
	            <img src='/img/phone.svg' width='100px' /><br/>
	            Flashez le QRCode situé à droite ==><br/>
	            <br/>
	            ou<br/>
	            <br/>
	            Aller sur <b>http://partyplayer.fr</b><br/>
	            Participer au JukeBox: <b><? echo $sessid ?></b><br/>
	        </div>
	        <div id='vote-area-ph'></div>
	    </div>
	    
	</div>
	
	<!-- Colonne de droite -->
	<div class='col-xs-12 col-sm-4' id='colonne_droite'>
	    
	    
	    <div class="panel panel-warning" id='share_panel'>
	        <div class="panel-heading">
	            <h3 class="panel-title" style="display:inline">Participer à la playlist
	            </h3>
	            <span onclick='$("#share_panel").hide();' style='float:right;cursor:pointer;'>X</span>
	        </div>
	        <div class="panel-body placeholders">
	             Votre JukeBox : <b><?php echo $sessid ?> <br/></b>
	             <img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=<?php echo urlencode($session_url); ?>&choe=UTF-8" /><br/>
	      </div>
	    </div>
	    
	    <?php include_once('_playlist.php'); ?>
	</div>