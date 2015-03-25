
function addSpotifyPlaylistToActualPlaylist(){
	var nb = 0;
	for(var index in my_convert_data){
		var id_youtube = my_convert_data[index];
		addToPlaylistOnServer(id_youtube);
		nb++;
	}
	if(nb>0){
		return true;
	}
	else{
		alert('Aucune chansons trouvées... Pas de conversion possible.');
		return false;
	}
}

//conversion vers youtube
function convertSpotify(){
	my_convert_data = [];//re-init
	console.log(my_import_data);
	
	for (var id_spotify in my_import_data){
		var track_name = my_import_data[id_spotify];
		console.log(track_name);
		//debut de la recherche sur youtube
		$('#track_spotify_'+id_spotify).append('&nbsp;<img src="/img/ajax_loader.gif" class="loader" width="20px" />');
		var cb = function(data,params){
			var cur_id_spotify = params;
			var duree = 0;
			var cur_elem = $('#track_spotify_'+cur_id_spotify);
			//que faire avec le resultat de la requete
			if(data != null && typeof data.feed.entry !== 'undefined' && data.feed.entry.length > 0){
				try {
					
					//on recherche d'abord les video venant de VEVO
					data.feed.entry.forEach(function(element,index,array){
				        var element = data.feed.entry[0];
				        if( typeof(element['media$group']) !== 'undefined' 
				        && typeof(element['media$group']['media$content']) !== 'undefined' 
				        && typeof(element['media$group']['media$content'][0]) !== 'undefined'
				        )
				        duree = element['media$group']['media$content'][0]['duration'];
				        //si le resultat vient de VEVO et ne contient pas des trucs trop court ou trop long (spam)
				        if(element['author'][0]['name'].indexOf('VEVO') !== false && duree > minDurationSearchTrack && duree < maxDurationSearchTrack){
				            throw BreakException;
			            } 
				    });
					
					//ensuite les autres chaine de video
				    data.feed.entry.forEach(function(element,index,array){
				        var element = data.feed.entry[0];
				        if( typeof(element['media$group']) !== 'undefined' 
				        && typeof(element['media$group']['media$content']) !== 'undefined' 
				        && typeof(element['media$group']['media$content'][0]) !== 'undefined'
				        )
				        duree = element['media$group']['media$content'][0]['duration'];
				        //si le resultat ne contient pas des trucs trop court ou trop long (spam)
				        if(duree > minDurationSearchTrack && duree < maxDurationSearchTrack){
				            throw BreakException;
			            } 
				    });
				} 
				//si une chanson assez longue est trouvé, on break le foreach, et on l'ajoute à la liste
				catch(e) {
					var element = data.feed.entry[0];
				    var id_youtube = element['media$group']['yt$videoid']['$t'];
				    cur_elem.find('.loader').hide();
			        cur_elem.append('&nbsp;<img src="/img/check.svg" class="check" width="20px" />');
			        my_convert_data.push(id_youtube);
				}
			}
			//si rien n'est trouvé
			if(duree == 0){
				cur_elem.find('.loader').hide();
				cur_elem.append('&nbsp;<img src="/img/fail.svg" class="fail" width="20px" />');
			}
			
		}
		
		searchTrackOnYoutube(track_name,cb,id_spotify);
	}
}

//Listing des chasnons d'une playlist spotify
function importSpotifyPlaylist(href) {
	BB.hide();
    $.getJSON(spotifyApiURL, {
	    'custom': href,
	    //'sessid': sessid,
	    //'user': username
	}, function (data) {
		//Construction de la présentation de la playlist
		my_import_data = [];
		var message = "<ul>";
		data.content.tracks.items.forEach(function(element,index,array){
			
			if (element.track.name != "" && element.track.id != ""){
		    	var track_name = element.track.artists[0].name+" - "+element.track.name;
		    	var id= element.track.id;
		    	message += "<li href='#' id='track_spotify_"+id+"'>"+track_name+"</li>";
		    	my_import_data[id] = track_name;
		    }
		});
		message += "</ul>";
		
		BB = bootbox.dialog({
		    message: message,
		    title: "Playlist : "+data.content.name,
		    closeButton: true,
		    buttons: {
		      success: {
				label: "Ajouter les chansons à la playlist",
				className: "btn-success",
				callback: function() {
				    return addSpotifyPlaylistToActualPlaylist();
				}
			  }
		    }
		  });
		  
		  //lancement de la conversion
		  convertSpotify();
		
	}
    );
    
    
}

//Listing des playlist importable depuis spotify
function importSpotify(){
    if(jQuery.cookie("spotify_token") != ""){
        $.getJSON(spotifyApiURL, {
	    'get_playlists': 'true',
	    //'sessid': sessid,
	    //'user': username
	}, function (data) { 
	    if(data.result == 'error'){
		bootbox.alert(data.error);
	    }
	    else{
		var message = "<ul>";
		data.content.forEach(function(element,index,array){
		    if (element.name != "" && element.tracks_num > 0)
		    message += "<li> <a href='#' onclick='importSpotifyPlaylist(\""+element.href+"\");'>"+element.name+"</a> ("+element.tracks_num+" titres)</li>";
		});
		message += "</ul>";
		
		BB = bootbox.dialog({
		    message: message,
		    title: "Vos playlists sur Spotify",
		    closeButton: true,
		    buttons: {
		      main: {
			label: "Fermer",
			className: "btn-primary",
			callback: function() {
			    BB.hide();
			  //Example.show("Primary button");
			}
		      }
		    }
		  });
		
	    }
	});
    }
    else{
	//on rafraichis la page pour afficher le lien de connexion spotify
	window.location.href.reload;
    }
}

function markAllAsUnread(){
    $.getJSON(serverURL, {
        'mode': 'unread_all',
        'sessid': sessid,
        'user': username
    }, function (data) { 
        if(data.result == 'error'){
            bootbox.alert(data.error);
        }
        else{
            lastPlayedId = 0;
	        loadPlaylistFromServer();
        }
    });
}

//construire le html d'un item de playlist, sur le serveur
function buildHTMLPlaylistItem(element,title,user,vote,date){
    var id = element.id;
    var d = new Date();
    d.setTime(element.addTime*1000);
    var html = '<div id="'+element.id+'" ';
    html += 'data-add-date="'+element.addTime+'" ';
    html += 'data-vote="'+element.vote+'" ';
    html += 'data-user="'+element.addUser+'" ';
    html += 'class="list-group-item ';
    if(element.alreadyRead){
        html += 'alreadyRead';
    }
    html += '" ';
    html += 'style="float: left; width: 100%;" ';
    html += '>';
    
    html += '<div class="playlist_item_title">'+title+'</div>';
    
    //ligne de date
    html += '<div class="playlist_item_ligne" style="float:left" >';
        html += '<div class="playlist_item_user"> Ajouté par '+element.addUser+'</div>';
        html += '<div class="playlist_item_date"> le '+d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear()+' à '+d.getHours()+':'+('0'+d.getMinutes()).slice(-2)+'</div>';
    html += '</div>';
    
    
    html += '<div class="playlist_item_ligne" id="vote_ligne_'+id+'" style="float:right" >';
    //bouton lecture
    html += '<button type="button" class="btn btn-success" title="Lire ce titre" onclick="loadByYoutubeId(\''+id+'\')"><span class="glyphicon glyphicon-play"></span></button>&nbsp;';
    //bouton de suppression
    html += '<button type="button" class="btn btn-danger" title="Supprimer ce titre" onclick="deleteFromPlaylistOnServer(\''+id+'\')"><span class="glyphicon glyphicon-remove"></span></button>&nbsp;';
    //bouton de resultat des votes
    html += '<button type="button" class="btn btn-default" title="Qui a voté?" onclick="afficheVote(\''+id+'\')" style="padding: 4px 8px;" ><span class="glyphicon ';
    if(element.vote >= 0){
        html += 'glyphicon-thumbs-up';
    }
    else{
        html += 'glyphicon-thumbs-down';
    }
    html += '"></span>&nbsp;'+element.vote+'</button>&nbsp;';
    
    
    html += '</div>';
    
        
    html += '</div>';
    
    
    html += '<div style="display:none" id="detail_vote_'+id+'" >';
        html += ''+title+'';
        if(element.votes.length == 0){
            html += '<br/><br/>Pas de votant...<br/><br/>';
        }
        else{
            html += '<ul>';
            $.each(element.votes,function(index,elem){
               html += '<li>';
               if(elem.value == 'plus'){
                   html += '<span class="glyphicon glyphicon-thumbs-up"></span>';
               }
               else{
                   html += '<span class="glyphicon glyphicon-thumbs-down"></span>';
               }
               //html += elem.value;
               html += '&nbsp;'+elem.user+'</li>'; 
            });
            html += '</ul>';
        }
    html += ' </div>'
    
    
    return html;
}

function loadNextTrack(){
    console.log('loading next track');
	$.getJSON(serverURL, {
        'mode': 'next_track',
        'sessid': sessid,
        'last_played': lastPlayedId,
        'user': username
    }, function (data) { 
        if(data.result == 'error' && data.error == 'no_data'){
            //si la playlist est vide on fait rien
            playlistVide();
        }
        else if(data.result == 'error'){
            bootboxInstanceLoadNextTrack = bootbox.alert(data.error,function(){
                bootboxInstanceLoadNextTrack = null;
            });
            playerIsLoaded = false;
        }
        else{
	        if(typeof data.content !== 'undefined' && data.content != 0 && data.content != '' && data.content != 'undefined'){
            	loadByYoutubeId(data.content);
            	if(typeof bootboxInstanceLoadNextTrack !== 'undefined' && bootboxInstanceLoadNextTrack != null){
                    bootboxInstanceLoadNextTrack.modal('hide');
                }
            }else{
	            playerIsLoaded = false;
            }
        }
    });
}

function loadByYoutubeId(id){
	lastPlayedId = id;
    mediaPlayer_id = id;
    
    //var url = "https://www.youtube.com/v/"+id+"?version=3&f=videos&app=youtube_gdata";
    if(modeAudio == true){
        var url = "http://youtubeinmp3.com/fetch/?video=http://www.youtube.com/watch?v="+id;
    }
    else{
        var url = "http://www.youtube.com/watch?v="+id;
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
    
    indicateurLecture(id);
    
    markAsRead(id);
}

function load(url){
    playerIsLoaded = true;
    var htmlin = '<video id="audio-player" width="640"  height="360" style="max-width:100%;height:100%;" preload="auto" autoplay controls="controls"><source type="video/youtube" src="'+url+'" /></video>';
    // width="'+player_width+'" height="'+player_height+'"
    //version uniquement audio
    if(url.indexOf("mp3") != -1){
        htmlin = '<audio id="audio-player" autoplay="true" preload="none" src="'+url+'" type="audio/mp3" controls="controls"/></audio>';
    }
    
    jQuery('#player-wrapper').html(htmlin);
    
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

};


