function setUsername(user){
    username = user;
    jQuery('#username').html(username);
    jQuery.cookie("username",username);
}

function changeUsername(){
    bootbox.prompt({
        title: "Quel est votre nom?",
        value: jQuery.cookie("username"),
        callback:function(result) {                
          if (result === null || result == '') {                                             
            bootbox.alert("Pas de nom, pas de chocolat...");                              
          } else {
            setUsername(result);    
          }
        }
    });
}

function afficheVote(id){
    bootbox.dialog({
      message: $('#detail_vote_'+id).html(),
      title: "Qui a voté?",
      buttons: {
        main: {
          label: "Ok!",
          className: "btn-primary",
          callback: function() {
            //Example.show("Primary button");
          }
        }
      }
    });
}

function addToPlaylistOnServer(id,hide_message){
	var hide = false;
	if(typeof hide_message !== 'undefined' && hide_message == true){
		hide = true;
	}
    $.getJSON(serverURL, {
        'mode': 'add',
        'sessid': sessid,
        'id': id,
        'user': username
    }, function (data) { 
        if(data.result == 'error'){
            if(data.error=='no_file'){
		        jQuery.getJSON(serverURL, {
    		        'mode': 'create',
    		        'sessid': sessid
    		    },function(data){
        		    //que faire aprés la création?
    		    });
	        }
	        else{
		        if(!hide)
		        bootbox.alert(data.error);
	        }
        }
        else{
            var cb = function(){
                cible('#'+id);
                $('#'+id).find(".playlist_item_title").css('font-weight','bold');
            }
            loadPlaylistFromServer(cb);
        }
    });

}

function indicateurLecture(id){
    //activation de la bonne ligne dans la playlist
    $('#playlist .glyphicon-play-circle').remove();
    $('#playlist .list-group-item').removeClass('active');
    $('#'+id).addClass('active');
    
    //ajout du glyphicon de lecture
    /*var orig = $('#'+id).html();
    $('#'+id).html('<span class="glyphicon glyphicon-play-circle"></span>&nbsp;'+orig);*/
}

function playlistVide(){
	$('#playlist').html("Playlist vide...");
	messagePartage();
}

function messagePartage(){
	message('success',"Partagez avec vos amis!","Lien: <a href='"+session_url+"' target='_new' >"+session_url+"</a> ou le JukeBox CODE <b> <a target='_new' href='"+session_url+"' >"+sessid+"</a></b> ");
}

function cible(element){
    jQuery('html, body').animate({
        scrollTop: jQuery(element).offset().top - marge_header
    }, 1000);
}

function deleteFromPlaylistOnServer(id){
    
    cb_confirm = function(data){
        var titre = '';
        if(data.entry.title['$t'] != ''){
            titre = data.entry.title['$t'];
        }
        
        bootbox.confirm("Etes vous sur de vouloir supprimer '"+titre+"' ?",function(result){
            if(result){
                $.getJSON(serverURL, {
                    'mode': 'delete',
                    'sessid': sessid,
                    'id': id,
                    'user': username
                }, function (data) { 
                    if(data.result == 'error'){
                        bootbox.alert(data.error);
                    }
                    else{
                        loadPlaylistFromServer();
                    }
                });
            }
        });
    }
    
    getYoutubeTrackInfo(id,cb_confirm);
}

function markAsRead(id){
    $.getJSON(serverURL, {
        'mode': 'mark_read',
        'sessid': sessid,
        'id': id,
        'user': username
    }, function (data) { 
        if(data.result == 'error'){
            bootbox.alert(data.error);
        }
        else{
            loadPlaylistFromServer();
        }
    });
}

//Fonction de messagerie
//type= success, info, warning, danger
function message(type,titre,message){
	contenu = "";
	if(type != ''){
		contenu += "<div class='alert alert-"+type+"' role='alert'>";
	}
	else{
		contenu += "<div class='alert alert-success' role='alert'>";
	}
	
	if(titre != ''){
		contenu += " <strong>"+titre+"</strong> ";
	}
	
	contenu += message+"<span onclick='$(\"#message\").hide();' style='float:right;cursor:pointer;'>X</span></div>";
	
	$("#message").html(contenu);
	$("#message").show();
}

function loadPlaylistFromServer(callback){
	if(sessid != ''){
		//verification de l'anti flood
		if( ((new Date()).getTime() - lastPlaylistLoading) > playlistMinRefreshTime){
			//chargement de la playlist depuis le serveur
		    jQuery.getJSON(serverURL, {
		        'mode': 'read',
		        'sessid': sessid,
		        'lastUpdateTime': lastUpdateTime
		    }, function (data) { 
		        if(data.result == 'error'){
    		        if(data.error=='no_file'){
        		        jQuery.getJSON(serverURL, {
            		        'mode': 'create',
            		        'sessid': sessid
            		    },function(data){
                		    //que faire aprés la création?
                		    //playlistVide();
                		    if(typeof callback !== 'undefined'){
    				            callback();
				            }
            		    });
    		        }
    		        else{
        		        bootbox.alert(data.error);
    		        }
		        }
		        else{
			        if(data['content'] == 'no_data'){
				        playlistVide();
				        if(typeof callback !== 'undefined'){
				            callback();
			            }
			        }
			        else if(data['content'] != 'no_news'){
				        donnee = data['content'];//JSON.parse(data['content']);
				        lastPlaylistLoading = (new Date()).getTime();
				        if(donnee['lastUpdateTime'] > lastUpdateTime){
					        lastUpdateTime = donnee['lastUpdateTime'];
					        $('#playlist').html('');//on vide la playlist
					        
					        tab_element = new Array();
					        $.each(donnee['items'], function(index,element){
						        tab_element.push(element);
				            });
				            //construit la playlist
				            buildLocalPlaylistRecursive(tab_element,function(){
    				            //a la fin si on est pas déja entrain de lire, on lance une lecture
    				            if(mode == 'server' && playerIsLoaded == false && autoPlay == true){
        				            loadNextTrack();
    				            }
    				            else if(mode == 'client' && typeof donnee['lastReadID'] !== 'undefined'){
        				           displayLastRead(donnee['lastReadID']);
    				            }
    				            
    				            if(mode == 'server'){
        				            //ajout des separateur pour les listes deja lus ou non
        				            if($('#playlist-container .list-group-item:not(.alreadyRead)').length > 0)
        				                $('#playlist-container .list-group-item:not(.alreadyRead):first').before('<span style="width:100%; background-color:grey;text-align:center;float:left;color:white;"> Prochain titre:</span>');
        				            if($('#playlist-container .list-group-item.alreadyRead').length > 0)
        				                $('#playlist-container .list-group-item.alreadyRead:first').before('<span style="width:100%; background-color:grey;text-align:center;float:left;color:white;"> Déjà lu:</span>');
    				            }
    				            
    				            if(typeof callback !== 'undefined'){
        				            callback();
    				            }
				            });
				        }
			        }
			        else{
    			        if(typeof callback !== 'undefined'){
				            callback();
			            }
			        }
		        }
		    });
		}
		else{
			console.log("playlist antiflood");
			if(typeof callback !== 'undefined'){
	            callback();
            }
		}
	}
	else{
		console.log("pas de session ID définie");
	}
}

//construit une playlist, en async, mais dans l'ordre
function buildLocalPlaylistRecursive(tab_element,cb){
	if(tab_element.length > 0){
		element = tab_element.shift();
		var callback = function (data) {
	        var title = data.entry.title['$t'];
	        var contenu = buildHTMLPlaylistItem(element,title);
	        $('#playlist').append(contenu);
	        buildLocalPlaylistRecursive(tab_element,cb);
	    };
	    var callback_error = function (data) {
		    //en cas d'erreur on passe à la chanson d'aprés
		    //TODO: inserer un message d'erreur
	        buildLocalPlaylistRecursive(tab_element,cb);
	    };
	    getYoutubeTrackInfo(element.id,callback);
    }
    else{
        if(typeof cb == 'function'){
            cb();
        }
    }
}

function searchTrackOnYoutube(query,callback,cb_params){
	var youtube_url = "https://gdata.youtube.com/feeds/api/videos?q="+encodeURI(query)+"&v=2&alt=json";
    $.ajax(youtube_url, {
	    dataType: "json",
	    error: function(){
		    if(typeof callback !== 'undefined' && typeof cb_params !== 'undefined'){
		            callback(null,cb_params);
	            }
	            else if(typeof callback !== 'undefined' ){
		            callback(null);
	            }
	    },
	    success: function (data) {
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
	            if(typeof callback !== 'undefined' && typeof cb_params !== 'undefined'){
		            callback(data,cb_params);
	            }
	            else if(typeof callback !== 'undefined' ){
		            callback(data);
	            }
	        }
    	}
    });
}

function getYoutubeTrackInfo(id,callback,callback_error){
    //si on a deja les informations en local
    if(typeof youtubeTrackInfo[id] != 'undefined'){
        if(typeof callback == 'function'){
            callback(youtubeTrackInfo[id]);
        }
    }
    //sinon on les cherche sur youtube
    else{
        var url = "https://gdata.youtube.com/feeds/api/videos/"+id+"?alt=json";
        $.ajax({
		  dataType: "json",
		  url: url,
		  data: {},
		  success: function (data) { 
            youtubeTrackInfo[id] = data;
            if(typeof callback == 'function'){
                callback(data);
            }
          },
          error: function(data){
	         if(typeof callback_error == 'function'){
                callback_error(data);
              } 
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
             addToPlaylistOnServer(id,true);     
        });
    };
    getYoutubePlaylistInfo(playlistId,callback);
}

function downloadById(id){
    var url = "http://youtubeinmp3.com/fetch/?video=http://www.youtube.com/watch?v="+id;
    window.open(url); 
    return false;
}

$(document).ready(function(){
    //récupération du username si existant
    username = "";
    if($.cookie('username') != undefined){
        username = $.cookie('username');
    }
    $('#username').html(username);
    
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
