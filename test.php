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
    <script src="/js/aw-party-player_common.js"></script>
    <script src="/js/jquery.cookie.js"></script>
    <script src="/js/bootbox.min.js" type="text/javascript"></script>
    <link rel="icon" type="image/png" href="/css/favicon.png" />

<script src="/js/build/mediaelement-and-player.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/js/build/mediaelementplayer.css" type="text/css">
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
        </body>
</html>