<?php
    
    $sessid = substr(md5(rand()), 0, 7);
    if(isset($_REQUEST['sessid']) ){
        $sessid = $_REQUEST['sessid'];
    }
    else{
        header('Location: ?sessid='.$sessid);
    }
    $base_url = "http://utube.weberantoine.fr";
    $session_url =  $base_url."?sessid=".$sessid;
    
    $marge_header = '65';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang='fr'>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="uTube Party Player">
    <meta name="author" content="WEBER Antoine">
    <script src="build/jquery.js" type="text/javascript"></script>
    <script src="build/mediaelement-and-player.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="build/mediaelementplayer.css" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
    <script src="js/bootbox.min.js" type="text/javascript"></script>
    <link rel="icon" type="image/png" href="/css/favicon.png" />
    <title>uTube Party Player</title>
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
        
        .playlist_item_ligne2{
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
    </style>
    
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img src='/css/favicon.png' height='20px' /> uTube Party Player</a>
        </div>
        <div id="nav-main" class="navbar-collapse collapse">
            
            
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Help</a></li>
            <li style='margin-top:10px;'>
                <div class="btn-group btn-toggle" id='toggle-audio'> 
                    <button class="btn btn-sm btn-primary active">Video</button>
                    <button class="btn btn-sm btn-default">Audio</button>
                </div>
            </li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Artiste ou titre..." id='recherche'></input>
            <span style="color:white">ou</span>
            <input type="text" class="form-control" placeholder="Playlist..." id='recherche-playlist'></input>
          </form>
        </div>
      </div>
    </div>
    
    
    <div class='row'>
        <div class='col-xs-12 col-sm-8'>
            
            <div class='well first_row placeholders'>
                <h2 id='main-title'>Bienvenue sur uTube Party Player!</h2>
                <div id='player-wrapper' width="50%" style="width:100%;position:relative;">
                    Commencez par rechercher une musique et l'ajouter à la playlist<br/>
                    <br/>
                    Demandez à vos amis d'ajouter des chansons...<br/>
                    et créez ensemble en live votre uTube Party!<br/>
                    
                    
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
        <div class='col-xs-12 col-sm-4'>
            
            
            <div class="panel panel-primary row first_row " >
                <div class="panel-heading">
                    <h3 class="panel-title">Votre Session
                    </h3>
                </div>
                <div class="panel-body placeholders">
                     <b><div id='username' style='display:inline;'></div></b> <span class='glyphicon glyphicon-edit' onclick='bootbox.prompt("Quel est votre nom?", function(result) {                
  if (result !== null) {                                             
    changeUsername(result);                          
  }
});'></span><br/>
                     Partager votre playlist:<br/>
                     <a href="<?php echo $session_url; ?>"><?php echo $session_url; ?></a><br/>
                     <img src="https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=<?php echo urlencode($session_url); ?>&choe=UTF-8" /><br/>
                    
              </div>
            </div>
            
            
            <div class="panel panel-primary row" id='playlist-container'>
                <div class="panel-heading">
                    <h3 class="panel-title">Playlist partagée&nbsp; &nbsp; 
                    <div class="dropdown" style="display:inline;" >
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class='glyphicon glyphicon-tasks'></span>&nbsp;<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="#">Nouvelle playlist</a>
                            </li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="#">Vider la playlist</a>
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="#">Enregistrer la playlist</a>
                            </li>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="#">Charger une playlist</a>
                            </li>
                        </ul>
                    </div>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="list-group" id='playlist'>
                    </div> 
              </div>
            </div>
            
            <span onclick="alertVoteNext()">test vote</span>
            
            <span onclick="testApi()">test api</span>
               
        </div>
    
    </div>
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
        serverURL = "server.php";
        modeAudio = false;
        
        mediaPlayer = null;
        mediaPlayer_id = null;
        
        voteEnCours = {};
        
        username = '<?php echo "Anonyme".substr(rand(),0,5); ?>';//username par défaut si inexistant
        
        $(document).ready(function()
        {
            //récupération du username si existant
            if($.cookie('username') != undefined){
                username = $.cookie('username');
            }
            $('#username').html(username);
            
            //charge l'état existant de la playlist
            loadPlaylistFromServer();
            
            //rend la playlist drag n drop
            $("#playlist").sortable({
              update: function( event, ui ) {
                  savePlaylistOnServer();
              }
            });
            
            //gére la touche entré sur la recherche
            $("#recherche").keypress(function( event ) {
                if ( event.which == 13 ) {
                    event.preventDefault();
                    searchYoutube($("#recherche").val());
                    $("#nav-main").collapse('hide');
                }
            });
            
            $("#recherche-playlist").keypress(function( event ) {
                if ( event.which == 13 ) {
                    event.preventDefault();
                    searchPlaylistOnYoutube($("#recherche-playlist").val());
                    $("#nav-main").collapse('hide');
                }
            });
            
            //toggle audio
            $('#toggle-audio').click(function() {
                changeModeAudio();
            });
            
            //toggle css
            $('.btn-toggle').click(function() {
                $(this).find('.btn').toggleClass('active');  
                
                if ($(this).find('.btn-primary').size()>0) {
                	$(this).find('.btn').toggleClass('btn-primary');
                }
                if ($(this).find('.btn-danger').size()>0) {
                	$(this).find('.btn').toggleClass('btn-danger');
                }
                if ($(this).find('.btn-success').size()>0) {
                	$(this).find('.btn').toggleClass('btn-success');
                }
                if ($(this).find('.btn-info').size()>0) {
                	$(this).find('.btn').toggleClass('btn-info');
                }
                
                $(this).find('.btn').toggleClass('btn-default');
            });
            
        });
        
        function testApi(){
            data = {
                "acces_token" :'<?php echo $token ;?>',
                "method": "track_search",
                "query" : "skrillex"
                
            };
            jQuery.getJSON("http://api.pleer.com/index.php", data , function(data){
                console.log(data);
            });
        }
        
        function changeUsername(new_name){
            $.cookie('username', new_name, { expires: 999, path: '/' });
            username = new_name;
            $('#username').html(username);
        }
        
        function changeModeAudio(){
            modeAudio = !modeAudio;
            if(!mediaPlayer.paused){
                mediaPlayer.pause();
                loadByYoutubeId(mediaPlayer_id);
            }
        }
        
        function cible(element){
            jQuery('html, body').animate({
                scrollTop: jQuery(element).offset().top <?php echo '-'.($marge_header); ?>
            }, 1000);
        }
        
        //permet d'ajouter un vote sur une chanson
        function vote(id,vote){
            jQuery.getJSON(serverURL, {
                'mode': 'vote',
                'sessid': sessid,
                'user': username,
                'youtube_id': id,
                'vote': vote
            }, function (data) { 
                if(data.result == 'error'){
                    bootbox.alert(data.error);
                }
                else{
                    loadPlaylistFromServer();
                }
            });
        }
        
        function compte_a_rebours(id,timer)
        {
            if(voteEnCours[id]){
                timer -= 1;
                if(timer > 0){
                	jQuery('#next-counter').html(timer)
                	var actualisation = setTimeout("compte_a_rebours('"+id+"','"+timer+"');", 1000);
            	}
            	else{
                	bootbox.hideAll();
                	loadNextTrack();
                	voteEnCours[id] = false;
            	}
        	}
        }
        
        //previens lors d'un nombre de vote négatif, et passe à la suivante
        function alertVoteNext(id){
            voteEnCours[id] = true;
            var dialog = bootbox.dialog({
              message: "<div class='placeholders'>Un vote négatif a été mis sur la chanson actuelle.<br/>La chanson va changer dans: <b><div id='next-counter'>20</div></b> secondes </div>",
              title: "Chanson suivante...?",
              buttons: {
                success: {
                  label: "On laisse! <span class='glyphicon glyphicon-thumbs-up'></span> ",
                  className: "btn-success",
                  callback: function() {
                    bootbox.hideAll();
                    voteEnCours[id] = false;
                  }
                },
                danger: {
                  label: "Suivant! <span class='glyphicon glyphicon-thumbs-down'></span> ",
                  className: "btn-danger",
                  callback: function() {
                    loadNextTrack();
                    voteEnCours[id] = false;
                  }
                }
              }
            });
            
            var timer = 20;
            compte_a_rebours(id,timer);
        }
        
        function loadNextTrack(mediaElement, domObject)//argument inutile fournie par le mediaplayer
        {
            var next = jQuery("#playlist .active" ).next();
            if(next.length > 0){
                loadByYoutubeId(next.attr('id'));
            }
        };
        
        //ce user ajoute a la suite du courant, et play
        function addAsNextAndLoad(id){
            //si l'element existe deja dans la liste
            if(jQuery('#'+id).length > 0){
                loadByYoutubeId(id);
            }
            //sinon on l'ajoute
            else{
                var callback = function (data) {
                    var title = data.entry.title['$t'];
                    var contenu = buildHTMLPlaylistItem(id,title,username,0,(new Date()).getTime());
                    if(jQuery('#playlist .active').length > 0){
                        jQuery('#playlist .active').after(contenu);
                    }
                    else{
                        jQuery('#playlist').prepend(contenu);
                    }
                    loadByYoutubeId(id);
                };
                getYoutubeTrackInfo(id,callback);
            }
        }
        
        function getYoutubeTrackInfo(id,callback){
            //si on a deja les inforamtions en locale
            if(typeof youtubeTrackInfo[id] != 'undefined'){
                if(typeof callback == 'function'){
                    callback(youtubeTrackInfo[id]);
                }
            }
            //sinon on les cherche sur youtube
            else{
                var url = "https://gdata.youtube.com/feeds/api/videos/"+id+"?alt=json";
                $.getJSON(url, function (data) { 
                    youtubeTrackInfo[id] = data;
                    if(typeof callback == 'function'){
                        callback(data);
                    }
                });
            }
        }
        
        function getYoutubePlaylistInfo(playlistId,callback){
            //si on a deja les inforamtions en locale
            if(typeof youtubePlaylistInfo[playlistId] != 'undefined'){
                if(typeof callback == 'function'){
                    callback(youtubePlaylistInfo[playlistId]);
                }
            }
            //sinon on les cherche sur youtube
            else{
                var url = "https://gdata.youtube.com/feeds/api/playlists/"+playlistId+"?alt=json&v=2";
                jQuery.getJSON(url, function (data) { 
                    youtubePlaylistInfo[playlistId] = data;
                    if(typeof callback == 'function'){
                        callback(data);
                    }
                });
            }
        }
        
        function loadPlaylistFromServer(){
            jQuery.getJSON(serverURL, {
                'mode': 'read',
                'sessid': sessid
            }, function (data) { 
                if(data.result == 'error'){
                    bootbox.alert(data.error);
                }
                else{
                    donnee = JSON.parse(data['content']);
                    $.each(donnee, function(index,element){
                        addToPlayListLocal(element.id,element.user,element.vote,element['add-date']);
                    });
                }
            });
        }
        
        function savePlaylistOnServer(){
            //sauvegarder la playslist complete sur le serveur
            if(isNewPlaylist){
                var playlist = {};
                var liste = $('#playlist .list-group-item');
                if(liste.length > 0){
                    liste.each(function(index,elem){
                        var element = $(elem);
                        var item = {};
                        item['id'] = element.attr('id');
                        var vote = 0;
                        if(element.data('vote') > 0){
                            vote = element.data('vote');
                        }
                        item['vote'] = vote;
                        if(element.data('add-date') > 0){
                            item['add-date'] = element.data('add-date');
                        }
                        user = username;
                        if(element.data('user') != ''){
                            user = element.data('user');
                        }
                        item['user'] = user;
                        playlist[item['id']] = item;
                    });
                    
                    $.getJSON(serverURL, {
                        'mode': 'write',
                        'sessid': sessid,
                        'playlist': playlist
                    }, function (data) { 
                        if(data.result == 'error'){
                            bootbox.alert(data.error);
                        }
                    });
                }
            }
        }
        
        function addToPlayListLocal(id,user,vote,date){
            var callback = function (data) {
                var title = data.entry.title['$t'];
                var contenu = buildHTMLPlaylistItem(id,title,user,vote,date);
                $('#playlist').append(contenu)
            };
            getYoutubeTrackInfo(id,callback);
            
        }
        
        function addToPlayList(id){
            var callback = function (data) {
                var title = data.entry.title['$t'];
                addPlaylistItem(id,title); 
                savePlaylistOnServer();
                cible('#playlist-container');
            };
            getYoutubeTrackInfo(id,callback);
            
        }
        
        function getPlaylistDetails(playlistId){
            var callback = function (data) { 
                var detail = "<table class='table table-striped table-hover'>";
                detail += "<thead>";
                detail += "<tr>";
                detail += "<th>#</th>";
                detail += "<th>Image</th>";
                detail += "<th>Titre</th>";
                detail += "<th>Actions</th>";
                detail += "</tr>";
                detail += "</thead>";
                detail += "<tbody id='result'>";
                
                data.feed.entry.forEach(function(element,index,array){
                     var id = element['media$group']['yt$videoid']['$t'];
                     var img = "";
                     if(typeof element['media$group']['media$thumbnail'] != 'undefined'){
                         img = element['media$group']['media$thumbnail'][0].url;
                     };
                     var title = "";
                     if(typeof element.title != 'undefined'){
                         title = element.title['$t'];
                     };
                     detail += buildHTMLResultatTrackItem(
                        index,
                        img,
                        title,
                        id
                    );
                });
                
                detail += "</tbody>";
                detail += "</table>";
                
                bootbox.dialog({
                  message: detail,
                  title: data.feed.title['$t']
                });
                
                //bootbox.alert(detail);
            };
            getYoutubePlaylistInfo(playlistId,callback);
        }
        
        function loadYoutubePlaylist(playlistId){
            var callback = function (data) { 
                data.feed.entry.forEach(function(element,index,array){
                     var id = element['media$group']['yt$videoid']['$t'];
                     addPlaylistItem(id,element.title['$t']);       
                });
            };
            getYoutubePlaylistInfo(playlistId,callback);
        }
        
        function downloadById(id){
            var url = "http://youtubeinmp3.com/fetch/?video=http://www.youtube.com/watch?v="+id;
            window.open(url); 
            return false;
        }
        
        function loadByYoutubeId(id){
            mediaPlayer_id = id;
            
            //var url = "https://www.youtube.com/v/"+id+"?version=3&f=videos&app=youtube_gdata";
            if(modeAudio == true){
                var url = "http://youtubeinmp3.com/fetch/?video=http://www.youtube.com/watch?v="+id;
            }
            else{
                var url = "https://www.youtube.com/watch?v="+id+"&feature=youtube_gdata";
            }
            load(url);
            
            //ajout du titre
            var callback = function(data){
                var titre = '';
                if(data.entry.title['$t'] != ''){
                    titre = data.entry.title['$t'];
                }
                jQuery('#main-title').html(titre);
            };
            getYoutubeTrackInfo(id,callback);
            
            //ajout des boutons de vote
            jQuery('#vote-area-ph').html("<br/><button type='button' class='btn btn-success' onclick='vote(\""+id+"\",\"plus\")'> J'aime! <span class='glyphicon glyphicon-thumbs-up'></span></button> <button type='button' class='btn btn-danger' onclick='vote(\""+id+"\",\"moins\")'> C'est nul! <span class='glyphicon glyphicon-thumbs-down'></span></button><br/><br/>");
            
            //activation de la bonne ligne dans la playlist
            $('#playlist .glyphicon-play-circle').remove();
            $('#playlist .list-group-item').removeClass('active');
            $('#'+id).addClass('active');
            //ajout du glyphicon de lecture
            /*var orig = $('#'+id).html();
            $('#'+id).html('<span class="glyphicon glyphicon-play-circle"></span>&nbsp;'+orig);*/
        }
        
        function load(url){
            var htmlin = '<video id="audio-player" width="640"  height="360" style="max-width:100%;height:100%;" preload="auto" autoplay controls="controls"><source type="video/youtube" src="'+url+'" /></video>';
            // width="'+player_width+'" height="'+player_height+'"
            //version uniquement audio
            if(url.indexOf("mp3") != -1){
                htmlin = '<audio id="audio-player" autoplay="true" preload="none" src="'+url+'" type="audio/mp3" controls="controls"/></audio>';
            }
            
           //if(mediaPlayer.pluginType == 'flash'){
                jQuery('#player-wrapper').html(htmlin);
                /*mediaPlayer = new MediaElementPlayer("#audio-player",
                {
                   alwaysShowControls: true,
                   features: ['playpause','volume','progress','fullscreen'],
                   audioVolume: 'horizontal',
                   audioWidth: player_width,
                   audioHeight: player_height,
                   success: function(mediaElement, domObject)
                   {
                       mediaElement.addEventListener('ended', loadNextTrack, false);
                       mediaElement.addEventListener('canplay', function() {
                            mediaPlayer.play();
                       }, false);
                        
                   }
                });*/
                
                
                mediaPlayer = new MediaElementPlayer("#audio-player",{
                    // if set, overrides <video width>
                    videoWidth: -1,
                    // if set, overrides <video height>
                    videoHeight: -1,
                    // width of audio player
                    audioWidth: '100%',
                    // height of audio player
                    audioHeight: 30,
                    // initial volume when the player starts
                    startVolume: 1,
                    // useful for <audio> player loops
                    loop: false,
                    // enables Flash and Silverlight to resize to content size
                    enableAutosize: true,
                    // the order of controls you want on the control bar (and other plugins below)
                    features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
                    // Hide controls when playing and mouse is not over the video
                    alwaysShowControls: false,
                    // force iPad's native controls
                    iPadUseNativeControls: false,
                    // force iPhone's native controls
                    iPhoneUseNativeControls: false, 
                    // force Android's native controls
                    AndroidUseNativeControls: false,
                    // forces the hour marker (##:00:00)
                    alwaysShowHours: false,
                    // show framecount in timecode (##:00:00:00)
                    showTimecodeFrameCount: false,
                    // used when showTimecodeFrameCount is set to true
                    framesPerSecond: 25,
                    // turns keyboard support on and off for this instance
                    enableKeyboard: true,
                    // when this player starts, it will pause other players
                    pauseOtherPlayers: true,
                    // array of keyboard commands
                    keyActions: [],
                    success: function(mediaElement, domObject)
                    {
                       mediaElement.addEventListener('ended', loadNextTrack, false);
                       mediaElement.addEventListener('canplay', function() {
                            mediaPlayer.play();
                       }, false);
                    }
                });
                
                
                
                
                
                
                mediaPlayer.play();
            /*}
            else{
               mediaPlayer.pause();
               mediaPlayer.setSrc({src: url, type:'video/youtube'});
               mediaPlayer.load();
               mediaPlayer.play();
            }*/
            cible('#player-wrapper');
        };
        
        //construire le html d'un item de playlist
        function buildHTMLPlaylistItem(id,title,user,vote,date){
            var html = '<div id="'+id+'" ';
            html += 'data-add-date="'+date+'" ';
            html += 'data-vote="'+vote+'" ';
            html += 'data-user="'+user+'" ';
            html += 'class="list-group-item" ';
            html += 'onclick="loadByYoutubeId(\''+id+'\')">';
            html += '<div class="playlist_item_title">'+title+'</div>';
                html += '<div class="playlist_item_ligne2">';
                    html += '<div class="playlist_item_user"> Ajouté par '+user+'</div>';
                    html += '<div class="playlist_item_date"> le '+date+'</div>';
                html += '</div>';
            html += '</div>';
            return html;
        }
        
        function buildHTMLResultatItem(index,col1,col2,action){
            var ligne = "<tr class='resultat'";
            ligne += "><td>";
            if(index != ''){
                ligne += index;
            }
            ligne +="</td><td>";
            if(col1 != '' && col1.indexOf('http') == 0){
                ligne += "<img height='50px' src='"+col1+"' ></img";
            }
            else if(col1 != ''){
                ligne += col1;
            }
            ligne +="</td><td>";
            if(col2 != ''){
                ligne += col2;
            }
            ligne +="</td><td>";
            if(action != ''){
                ligne += action;
            }
            ligne +="</td></tr>";
            return ligne;
        }
        
        function buildHTMLResultatPlaylistItem(index,image,titre,id){
            return buildHTMLResultatItem(
                index,
                image,
                titre,
                "<span class='glyphicon glyphicon-plus-sign big-glyph' onclick='loadYoutubePlaylist(\""+id+"\")' title='Ajouter toute la playlist à la suite'></span> <span class='glyphicon glyphicon-list-alt big-glyph' onclick='getPlaylistDetails(\""+id+"\")' title='Détail de la playlist'></span>"
            );

        };
        
        function buildHTMLResultatTrackItem(index,titre,resum,id){
            return buildHTMLResultatItem(
                index,titre,resum,
                "<span class='glyphicon glyphicon-plus-sign big-glyph' onclick='addToPlayList(\""+id+"\")' title='Ajouter à la playlist'></span>&nbsp;<span class='glyphicon glyphicon-play-circle big-glyph' onclick='addAsNextAndLoad(\""+id+"\")' title='Lecture directe'></span>&nbsp;<span class='glyphicon glyphicon-circle-arrow-down big-glyph' title='Téléchargement MP3' onclick='downloadById(\""+id+"\")'></span>"
            );
        };
        
        //ce user ajoute une chanson à la playlist
        function addPlaylistItem(id,title){
            
            if($('#'+id).length > 0){
                console.log('la video '+id+'est déja dans la liste'); 
                return false;
            }
            var contenu = buildHTMLPlaylistItem(id,title,username,0,(new Date()).getTime());
            $('#playlist').append(contenu);
        }
        
        function addResultat(index,image,titre/*,onclick*/,action){
            var ligne = buildHTMLResultatItem(index,image,titre,action);
            $('#result').append(ligne);
        };
        
        function videResultat(){
            $('#result').html('');
            var ligne = buildHTMLResultatItem('','','Aucun résultat');
            $('#result').append(ligne);
        };
        
        function searchPlaylistOnYoutube(query){
            var yt_url = "https://gdata.youtube.com/feeds/api/playlists/snippets?q="+encodeURI(query)+"&alt=json&v=2";
            $.getJSON(yt_url, function (data) {
                $('#result').html('');
                $('#reserach-result-title').html("Playlist: "+query);
                //si aucun resultat, on corrige l'orthographe
                if(typeof data.feed.entry == 'undefined' ){
                    var spell = '';
                    /*var title = '';
                    data.feed.link.forEach(function(element,index,array){
                        if(element.rel == "http://schemas.google.com/g/2006#spellcorrection"){
                            spell = element.href;
                            title = element.title;
                        }
                    });
                    if(spell != ''){
                        $('#recherche').val(title);
                        searchYoutubeUrl(spell);
                    }
                    else{
                        videResultat();
                    }*/
                }
                //sinon on affiche les resultat dans le tableau
                else{
                    data.feed.entry.forEach(function(element,index,array){
                        
                        var id = element['yt$playlistId']['$t'];
                        
                        var ligne = buildHTMLResultatPlaylistItem(
                            index,
                            element.title['$t'],
                            element.summary['$t'],
                            id
                        );
                        $('#result').append(ligne);
                    });
                    $('#search-result').show();
                    cible('#search-result');
                }
            });
        };
        
        function searchYoutube(query){
            var youtube_url = "https://gdata.youtube.com/feeds/api/videos?q="+encodeURI(query)+"&v=2&alt=json";
            $.getJSON(youtube_url, function (data) {
                $('#result').html('');
                $('#reserach-result-title').html("Résultat: "+query);
                //si aucun resultat, on corrige l'orthographe
                if(typeof data.feed.entry == 'undefined' ){
                    var spell = '';
                    var title = '';
                    data.feed.link.forEach(function(element,index,array){
                        if(element.rel == "http://schemas.google.com/g/2006#spellcorrection"){
                            spell = element.href;
                            title = element.title;
                        }
                    });
                    if(spell != ''){
                        $('#recherche').val(title);
                        searchYoutubeUrl(spell);
                    }
                    else{
                        videResultat();
                    }
                }
                //sinon on affiche les resultat dans le tableau
                else{
                    
                    data.feed.entry.forEach(function(element,index,array){
                        
                        var id = element['media$group']['yt$videoid']['$t'];
                        
                        var ligne = buildHTMLResultatTrackItem(
                            index,
                            element['media$group']['media$thumbnail'][0].url,
                            element.title['$t'],
                            id
                        );
                        $('#result').append(ligne);
                    });
                    $('#search-result').show();
                    cible('#search-result');
                }
            });
        }

    </script>
    
</body>
</html>