function AccueilStep2(){
	bootbox.prompt("Pour participer a une playlist, entrer son numero ID",function(retour){
        if(retour != ''){
	        window.location.href = '/?mode=client&sessid='+retour.toLowerCase();
        }
        else{
	        bootbox.confirm("Si vous n'avez pas de numéro ID, commencez une nouvelle playlist",function(){
		        window.location.href = '/?mode=server';
	        });
        }
    });
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
    var html = "<span style='float:right'>";
    html += "<span class='glyphicon glyphicon-plus-sign big-glyph' onclick='addToPlaylistOnServer(\""+id+"\");$(\"#search-result\").hide();' title='Ajouter à la playlist'></span>";
    //html += "&nbsp;<span class='glyphicon glyphicon-play-circle big-glyph' onclick='addAsNextAndLoad(\""+id+"\")' title='Lecture directe'></span>";
    //html += "&nbsp;<span class='glyphicon glyphicon-circle-arrow-down big-glyph' title='Téléchargement MP3' onclick='downloadById(\""+id+"\")'></span>";
    html += "</span>";
    return buildHTMLResultatItem(index,titre,resum,html);
};

//construire le html d'un item de playlist, sur le client
function buildHTMLPlaylistItem(element,title){
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
    html += 'style="float: left; width: 100%;" ';;
    html += '>';
    
    html += '<div class="playlist_item_title">'+title+'</div>';
    
    //ligne de date
    html += '<div class="playlist_item_ligne" style="float:left" >';
        html += '<div class="playlist_item_user"> Ajouté par '+element.addUser+'</div>';
        html += '<div class="playlist_item_date"> le '+d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear()+' à '+d.getHours()+':'+('0'+d.getMinutes()).slice(-2)+'</div>';
    html += '</div>';
    
    var alreadyVoted = false;
    $.each(element.votes,function(index,elem){
        if(elem.user == username || elem.token == vote_token){
            alreadyVoted = true;
        }
    });
        
    //bouton de vote
    if(!alreadyVoted){
        html += '<div class="playlist_item_ligne" id="vote_ligne_'+id+'" style="float:right" >';
            html += '<div class="playlist_item_votebutton"><button type="button" class="btn btn-success" onclick="vote(\''+id+'\',\'plus\')"><span class="glyphicon glyphicon-thumbs-up"></span></button> '+element.vote+' <button type="button" class="btn btn-danger" onclick="vote(\''+id+'\',\'moins\')"><span class="glyphicon glyphicon-thumbs-down"></span></button></div>';
        html += '</div>';
    }
    else{
        html += '<div class="playlist_item_ligne" id="vote_ligne_'+id+'" style="float:right" >';
        //bouton de resultat des votes
        html += '<button type="button" style="padding: 4px 8px;" class="btn btn-default" title="Qui a voté?" onclick="afficheVote(\''+id+'\')"><span class="glyphicon ';
        if(element.vote >= 0){
            html += 'glyphicon-thumbs-up';
        }
        else{
            html += 'glyphicon-thumbs-down';
        }
        html += '"></span>&nbsp;'+element.vote+'</button>&nbsp;';
        html += '</div>';
    }
        
    html += '</div>';
    
    
    //bootbox de resultat de vote
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

function displayLastRead(id){
    $('#last_play_content').html('');
    if($('#'+id).length > 0){
        var elem = $('#'+id);
        elem.detach().appendTo('#last_play_content');
        elem.find('.playlist_item_ligne').hide();
        elem.show();
        elem.attr('id','read_'+id);
        $('#last_play_wrapper').show();
    }
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
        $('#research-result-title').html("Playlist: "+query);
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
        $('#result_more_content').html('')
        $('#research-result-title').html("Résultat: "+query);
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
            
            var nb_result = 0;
            //pour chaque resultat venant de youtube
            data.feed.entry.forEach(function(element,index,array){
                var duree = 0;
                if(typeof (element['media$group']['media$content'][0]) !== 'undefined' )
                    duree = element['media$group']['media$content'][0]['duration'];
                //si le resultat ne contient pas des trucs trop court ou trop long (spam)
                if(duree > minDurationSearchTrack && duree < maxDurationSearchTrack){
                    nb_result++;
                    var id = element['media$group']['yt$videoid']['$t'];
                    
                    var ligne = buildHTMLResultatTrackItem(
                        index,
                        element['media$group']['media$thumbnail'][0].url,
                        element.title['$t'],
                        id
                    );
                    //premier resultat
                    if(nb_result <= 4){
                        $('#result').append(ligne);
                    }
                    //tableau de resultat supplémentaire
                    else{
                        $('#result_more').show();
                        $('#result_more_content').hide();
                        $('#result_more_content').append(ligne);
                    }
                }
            });
            $('#search-result').show();
            cible('#search-result');
        }
    });
}

/*function changeModeAudio(){
    modeAudio = !modeAudio;
    if(!mediaPlayer.paused){
        mediaPlayer.pause();
        loadByYoutubeId(mediaPlayer_id);
    }
}*/

//permet de positionner le champ visible sur un element
function cible(element){
    jQuery('html, body').animate({
        scrollTop: (jQuery(element).offset().top - marge_header)
    }, 1000);
}

function addToPlaylistOnServer(id){
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

//permet d'ajouter un vote sur une chanson
function vote(id,vote){
    jQuery.getJSON(serverURL, {
        'mode': 'vote',
        'sessid': sessid,
        'user': username,
        'item_id': id,
        'vote': vote,
        'vote_token': vote_token
    }, function (data) { 
        if(data.result == 'error'){
            bootbox.alert(data.error);
        }
        else{
            jQuery("#vote_ligne_"+id).hide();
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
/*function alertVoteNext(id){
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
}*/

/*function loadNextTrack(mediaElement, domObject)//argument inutile fournie par le mediaplayer
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
}*/

$(document).ready(function()
{
    
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
        }
    });
    
    $("#recherche-playlist").keypress(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
            searchPlaylistOnYoutube($("#recherche-playlist").val());
        }
    });
});