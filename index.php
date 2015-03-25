<?php
	header('Access-Control-Allow-Origin: *');
	if(!file_exists('api.php') || !file_exists('config.php')){
		echo "Probleme de déploiement, config.php ou api.php manquant!";exit;
	}
?>
<!DOCTYPE html>
<html lang='fr'>
<?php
    
    $rand_sessid = strtoupper(substr(md5(rand()), 0, 3));
    $sessid = '';
    if(isset($_REQUEST['sessid']) ){
        $sessid = $_REQUEST['sessid'];
    }
    
    $base_url = "http://".$_SERVER['SERVER_NAME'];
    $session_url =  $base_url."/JukeBox".$sessid;
    $serveur_url = "api.php";
    $spotify_api_url = "/includes/spotify.php";
    
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
    <script src="/js/aw-party-player_common.js"></script>
    <script src="/js/jquery.cookie.js"></script>
    <script src="/js/bootbox.min.js" type="text/javascript"></script>
    <link rel="icon" type="image/png" href="/css/favicon.png" />
    <title>Party Player - le JukeBox collaboratif de vos soirées!</title>
</head>

<body>
    <style>
        .row{
            margin-left: 15px;
            margin-right: 15px;
        }
        
        .first_row{
            margin-top:<?php echo $marge_header; ?>px;
        }
        
        .placeholders {
            text-align: center;
            margin-right: auto;
            margin-left: auto;
        }
        .playlist_item_ligne{
            font-size: 10px;
            display: inline;
        }
        .playlist_item_user{
            display: inline;
        }
        .big-glyph{
            font-size: 30px;
            line-height: 1.33;
            margin-right: 5px;
        }
        .navbar-header{
            width: 100%;
        }
        .navbar-brand>img {
            display: inline;
        }
        .alreadyRead{
           background-color: #f5f5f5;
           border: 1px solid #e3e3e3;
        }
        :not(#last_play_content *) .alreadyRead div.playlist_item_title{
            text-decoration: line-through;
        }
        input[type='text'],
        input[type='number'],
        textarea {
          font-size: 16px;
        }
        .footer {
			padding: 5px;
			position: absolute;
			bottom: 0;
			width: 100%;
			background-color: #f5f5f5;
			height: 30px;
		}
    </style>
    
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <!--<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>-->
          <a class="navbar-brand" href="/"><img src='/css/favicon.png' height='20px' /><span style='color:red;font-style:italic;'> Party Player </span><span id="subtitle">&nbsp;le JukeBox collaboratif de vos soirées!</span></a>
          <!--<div class='nav'><span>&nbsp; Transformer votre PC en Jukebox!<span></div>-->
        </div>
        
        <!--<div id="nav-main" class="navbar-collapse collapse">
            <a class="navbar-brand" id='username' onclick='changeUsername();' style='float:right'>UserName</a>     
        </div>-->
        
      </div>
    </div>
    <?php if(empty($_REQUEST['mode'])){ ?>
    <div class='container'>
	    <div class='row first_row'>
		    
	    <div class="jumbotron" id="jumbo1">
		  <h1><img src="/img/headphones.svg" alt="equalizer" style='width:70px;vertical-align: top;'/> Partagez votre musique!</h1>
		  <p>PartyPlayer permet de créer un Jukebox Collaboratif pour vos soirées!<br/>
			 <br/>
			 Le concept:<br/>
			 - Vos amis peuvent ajouter des musiques/clips<br/>
			 - Voter pour/contre les musiques<br/>
			 - Le jukebox lit en continue les musiques les mieux notées<br/>
			 - La soirée peut commencer!!!<br/>
			 <div class='text-center'><img src="/img/people.svg" width='120px' /></div><br/>
			 
		  </p>
		  <p><a class="btn btn-primary btn-lg" href="#" onclick="$('#jumbo1').hide();$('#jumbo2').show()" role="button">Comment ça marche?</a> <a class="btn btn-warning btn-lg" href="#" onclick="loadIntro()" role="button">Je connais déjà!</a></p>
		</div>
		
		<div class="jumbotron" id="jumbo2" style="display:none;">
		  <h1><img src="/img/computer.svg" width='100px'/> Ordi + <img src="/img/audio.svg" width='100px'/> Enceinte = JukeBox!</h1>
		  <p>Avec un ordinateur vous allez pouvoir créer votre jukebox.<br/>
			  <br/>
			 Le JukeBox va:<br/>
			 - Recevoir les musiques de vos amis<br/>
			 - Afficher les clips, et lire la musique sur ces enceintes<br/>
			 </p>
		  <p><a class="btn btn-primary btn-lg" href="#" onclick="$('#jumbo2').hide();$('#jumbo3').show()" role="button">Et comment ajouter des musiques?</a></p>
		</div>
		
		<div class="jumbotron" id="jumbo3" style="display:none;">
		  <h1>Ajouter des musiques, c'est facile!</h1>
		  <p>Avec votre smartphone <img src="/img/phone.svg" width='100px'/><br/>
			 <br/>
			 Scannez le QRCode de votre JukeBox<br/>
			 <br/>
			 ou<br/>
			 <br/>
			 - Allez sur http://partyplayer.fr <br/>
			 - Participez à la playlist, en tapant le CODE de votre JukeBox<br/>
			 </p>
		  <p><a class="btn btn-primary btn-lg" href="#" onclick="$('#jumbo3').hide();$('#jumbo4').show()" role="button">Ok! On Commence!</a></p>
		</div>
		
		<div class="jumbotron" id="jumbo4" style="display:none;">
		  <h1>Comment commencer?</h1>
		  <p>- Allez sur l'ordinateur <img src="/img/computer.svg" width='100px'/> qui va servir de JukeBox (pas sur un smartphone)<br/>
		  - Ouvrez le site http://partyplayer.fr<br/>
		  - Cliquez sur "Nouvelle Playlist"
		  </p>
		  <p><a class="btn btn-primary btn-lg" href="#" onclick="$('#jumbo4').hide();" role="button">Ok, je change d'ordi!</a>
		  <a class="btn btn-success btn-lg" href="#" onclick="window.location.href = '/?mode=server&sessid='+rand_sessid;" role="button">Je suis déjà sur le bon ordi!</a>
		  </p>
		</div>
		
	    </div>
    </div>
    <?php } ?>
    
    <script>
        
        /*TODO:
        - ajoutez les controles de clavier multimedia sur le player   
        - essayer de mixer 2 chansons pour les transitions
        - systeme de vote
        - systeme de déja lu 
        - mettre du cache dans loadPlaylistFromServer pour l'appeler régulierement
        - hit pour verifier les connectés, et supprimés automatiquement cron?
        - sauvegarder charge playlist
        - afficher la personne qui a ajouté et la date
        - afficher le nombre de personne connecté
        */
        
        //GLOBALE
        youtubeTrackInfo = {};
        youtubePlaylistInfo = {};
        isNewPlaylist = true;
        sessid = "<?php echo $sessid; ?>";
        rand_sessid = "<?php echo $rand_sessid; ?>";
        session_url = "<?php echo $session_url; ?>";
        serverURL = "<?php echo $serveur_url; ?>";
        spotifyApiURL = "<?php echo $spotify_api_url; ?>";
        modeAudio = false;
        lastPlaylistLoading = 0;
        lastUpdateTime = 0;
        playlistMinRefreshTime = 500;//0,5sec entre 2 requete
        marge_header = '<?php echo $marge_header; ?>';
        
        mediaPlayer = null;
        mediaPlayer_id = null;
        
        minDurationSearchTrack = 120;//2min
		maxDurationSearchTrack = 480;//8min
		
		//pour import spotify
		my_import_data = [];//denrieres infos de playlist d'import
		my_convert_data = [];//liste des id youtube a convertir
		BB = null; //derniere bootbox
        
        voteEnCours = {};
        mode = '';
        base_url= '<?php echo $base_url; ?>';
        
        //on cache le sous titre si le header est trop petit (mobile)
        if($('.navbar-header').outerWidth() < 530){
	        $('#subtitle').hide();
        }
      </script>   
        <?php 
            //lancement de l'appli
            if( !isset($_REQUEST['mode']) || ($_REQUEST['mode'] != 'server' && $_REQUEST['mode'] != 'client') ){ 
        ?>

        <script>
	        
	    function loadIntro(){
		    code_default = "CODE";
		    code_invalid = "CODE invalide!";
		    Intro = bootbox.dialog({
                title: "La soirée peut commencer!",
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-2 control-label" for="name">Pseudo</label> ' +
                    '<div class="col-md-6"> ' +
                    '<input id="name" name="name" type="text" value="'+username+
                    '" class="form-control input-md" /> </div>' +
                    '</div> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-2 control-label">&nbsp;</label> ' +
                    '<div class="col-md-6"> <div class="radio"> <label for="mode-server"> ' +
                    '<input type="radio" name="mode" id="mode-server" value="server" checked="checked"> ' +
                    'Nouveau Jukebox </label> ' +
                    '</div><div class="radio"> <label for="mode-client"> ' +
                    '<input type="radio" name="mode" id="mode-client" value="client"> Rejoindre un JukeBox existant </label> ' +
                    '<input type="text" value="'+code_default+'" id="code" name="code" style="display:none" />'+
                    '</div> ' +
                    '</div> </div>' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: "Ok",
                        className: "btn-success",
                        callback: function () {
                            setUsername($('#name').val());
                            var mode = $("input[name='mode']:checked").val();
                            if(mode=='client'){
	                            var code = $('#code').val();
	                            if(code=="CODE" || code=='' || code==code_default || code==code_invalid){
		                            $('#code').val(code_invalid);
		                            return false;
	                            }
	                            else{
		                            window.location.href = '/?mode=client&sessid='+code;
	                            }
	                            
                            }
                            else{
	                            window.location.href = '/?mode=server&sessid='+rand_sessid;
                            }
                        }
                    }
                }
            });
        
	        //ouverture de la boite CODE au select du radiobutton
	        $("input[name='mode']").change(function(){ $('#code').toggle();});
        
			Intro.on("hide", function() {    // remove the event listeners when the dialog is dismissed
				console.log('on hide');
		        bootbox.alert("Vous devez choisir de participer à un jukebox, ou d'en creer un nouveau...",function(){
			        console.log('callback');
			        document.location.reload();
		        });
		    });
	    }
		</script>
        <?php }else{
                $mode = $_REQUEST['mode'];
	        	echo " <script>
	        	    mode = '".$mode."';
                    </script>";
	        	include_once('./includes/_mode_'.$mode.'.php');
	        } ?>
        
        
        <!--<footer class="footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 item">
						&copy; 2015 <a href="http://www.weberantoine.fr">Weber A.</a>
					</div>
					<div class="col-sm-6 text-center">
						<iframe src="https://ghbtns.com/github-btn.html?user=TwanoO67&repo=party-player&type=star&count=true" frameborder="0" scrolling="0" width="170px" height="20px"></iframe>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="display:inline">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>

					</div>
				</div>
			</div>
		</footer>-->
</body>
</html>