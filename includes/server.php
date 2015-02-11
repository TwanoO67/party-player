<script src="/js/aw-party-player_server.js"></script>
<script src="build/mediaelement-and-player.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="build/mediaelementplayer.css" type="text/css">
<script>
<?php 
    if(isset($_COOKIE['username'])){
        echo "setUsername('".$_COOKIE['username']."');";
    }else{
        echo "setUsername('Serveur".substr(md5(rand()), 0, 3)."');";
    } 
	//Si l'url ne contient pas les infos necessaire on relance l'accueil step2
	if($sessid == ''){ ?>
		//AccueilStep2();
		window.location.href = '/?mode=server&sessid='+rand_sessid;
	<?php }
		else{
			echo "sessid = '".$sessid."';loadPlaylistFromServer();";
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
	        <h2 id='main-title'>Bienvenue sur Party Player!</h2>
	        <div id='player-wrapper' width="50%" style="width:100%;position:relative;">
	            Votre PartyPlayer est maintenant en attente de musique!<br/>
	            <br/>
	            <img src'/img/phone.svg' width='100px' /><br/>
	            Prenez maintenant votre <b>smartphone</b> : <br/>
	            --> Aller sur <b>http://partyplayer.fr</b><br/>
	            --> Participer à une playlist<br/>
	            --> Code: <b><? echo $sessid ?></b>)<br/>
	            <br/>
	            <br/>
	            ou Flashez le QRCode (situé sur la droite du site)
	        </div>
	        <div id='vote-area-ph'></div>
	    </div>
	    
	    <div id='search-result' class='placeholders' style='display:none' >
	        <h1 class="page-header" id='reserach-result-title'>Résultat de recherche</h1>
	        <table class="table table-striped table-hover">
	          <thead>
	            <tr>
	              <th>#</th>
	              <th>Image</th>
	              <th>Titre</th>
	              <th>Actions</th>
	            </tr>
	          </thead>
	          <tbody id='result'>
	          </tbody>
	        </table>
	        
	    </div>
	    
	
	</div>
	
	<!-- Colonne de droite -->
	<div class='col-xs-12 col-sm-4' id='colonne_droite'>
	    
	    
	    <div class="panel panel-warning" id='share_panel'>
	        <div class="panel-heading">
	            <h3 class="panel-title" style="display:inline">Partager votre playlist
	            </h3>
	            <span onclick='$("#share_panel").hide();' style='float:right;cursor:pointer;'>X</span>
	        </div>
	        <div class="panel-body placeholders">
	             <!--<b><div id='username' style='display:inline;'></div></b> <span class='glyphicon glyphicon-edit' onclick='changeUsername()'></span><br/>-->
	             ID : <b><?php echo $sessid ?> <br/></b>
	             <!--<a href="<?php echo $session_url; ?>"><?php echo $session_url; ?></a><br/>-->
	             <img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=<?php echo urlencode($session_url); ?>&choe=UTF-8" /><br/>
	      </div>
	    </div>
	    
	    <?php include_once('_playlist.php'); ?>
