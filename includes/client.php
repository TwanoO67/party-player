<script src="/js/aw-party-player_client.js"></script>
<script>
<?php 
    if(isset($_COOKIE['username'])){
        echo "setUsername('".$_COOKIE['username']."');";
    }else{
        echo "changeUsername();";    
    }
    	
	//si on a pas de token de vote, on en crée un
	if(!isset($_COOKIE['vote_token'])){
    	echo "$.cookie('vote_token','".substr(md5(rand()), 0, 5)."');";
	}
	
	//Si l'url ne contient pas les infos necessaire on relance l'accueil step2
	if($sessid == ''){ 
	?>
	    AccueilStep2();
<?php }
		else{
			echo "sessid = '".$sessid."';";
		}
		
	 ?>
	 
    loadPlaylistFromServer();
    setInterval(function() {
    	 //Verifier les nouvelles chansons dans la playlist toutes les 15sec
    	 loadPlaylistFromServer();
    }, 15000);
	 
    vote_token = $.cookie('vote_token');
	 
	
	 
</script>
<style>
    .alreadyRead{
        display: none;
    }
</style>

<div class='row first_row'>
	
	<div id='message'></div>
	
	<div class='col-xs-12 col-sm-8' id='colonne_gauche'>
        
        <div class='well placeholders'>
            <h2 id='main-title'>Bienvenue sur Party Player!</h2>
            <div id='player-wrapper' width="50%" style="width:100%;position:relative;">
                Rechercher une musique pour l'ajouter à la playlist du Jukebox<br/>
                <br/>
                Rechercher:<br/><br/>
	            <form>
		            <input type="text" class="form-control" placeholder="Artiste ou titre..." id='recherche' style='width:auto;display:inline'></input>
		            <input type='button' class="btn btn-default" onclick='searchYoutube($("#recherche").val());' value='Ok'></input> <br/>
		            ou<br/>
		            <input type="text" class="form-control" placeholder="Playlist..." id='recherche-playlist' style='width:auto;display:inline'></input>
                    <input type='button' class="btn btn-default" onclick='searchPlaylistOnYoutube($("#recherche-playlist").val());' value='Ok'></input> 
		         </form>
                
            </div>
            
            <div id='vote-area-ph'></div>
        </div>
        
        <div id='search-result' class='placeholders' style='display:none' >
            <h1 class="page-header" id='research-result-title'>Résultat de recherche</h1>
            <table class="table table-striped table-hover" style='margin-bottom:0;'>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Image</th>
                  <th>Titre</th>
                  <th>Ajouter</th>
                </tr>
              </thead>
              <tbody id='result'>
              </tbody>
            </table>
            <div id='result_more'>
                <input type='button' 
                    onclick="$('#result_more_content').show();$('#result_more').hide();" 
                    value='Plus de résultats' 
                    style='width:100%;'
                    />
            </div>
            <table class="table table-striped table-hover" >
              <tbody id='result_more_content' style='display:none'>
              </tbody>
            </table>
            
        </div>
        
    
    </div>

	<!-- Colonne de droite -->
	<div class='col-xs-12 col-sm-4'>
    	
    	
    	
    	<div class="panel panel-success" id='last_play_wrapper' style='display:none'>
            <div class="panel-heading" style='float:left;width:100%;'>
                <h3 class="panel-title">Dernière lecture</h3>
            </div>
            <div class="panel-body" style='padding:0;'>
                <div class="list-group" id='last_play_content'>
                </div> 
          </div>
        </div>
    	
    	
    	
    	<?php include_once('_playlist.php'); ?>
	</div>

</div>
