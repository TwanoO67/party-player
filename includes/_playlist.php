 
<div class="panel panel-info" id='playlist-container'>
    <div class="panel-heading" style='float:left;width:100%;'>
        <h3 class="panel-title">
        <?php if($mode=="server"){ ?>
        Playlist partagée&nbsp;(triée par vote...)
        <div class="dropdown" style="display:inline;float:right;" >
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class='glyphicon glyphicon-tasks'></span>&nbsp;<span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu" role="menu" style="left:auto;right:0;">
                <!--<li role="presentation">
                    <a role="menuitem" tabindex="-1" href="#">Nouvelle playlist</a>
                </li>
                <li role="presentation">
                    <a role="menuitem" tabindex="-1" onclick='deleteAll()'>Vider la playlist</a>
                </li>-->
                <li role="presentation">
                    <a role="menuitem" tabindex="-1" onclick='markAllAsUnread();'>Marquer comme non-lus</a>
                </li>
                <li role="presentation">
                	<a role="menuitem" tabindex="-1" class='spotify_import_button'>Spotify</a>
                </li>
                <!--<li role="presentation" class="divider"></li>
                <li role="presentation">
                    <a role="menuitem" tabindex="-1" href="#">Enregistrer la playlist</a>
                </li>
                <li role="presentation">
                    <a role="menuitem" tabindex="-1" href="#">Charger une playlist</a>
                </li>-->
            </ul>
        </div>
        <?php }else{
                echo "Playlist partagée&nbsp;(triée par vote...)";
            } ?>
        </h3>
    </div>
    <div class="panel-body" style='padding:0;'>
        <div class="list-group" id='playlist'>
	        <img src="/img/ajax_loader.gif" width="25px" />
        </div> 
  </div>
</div>
