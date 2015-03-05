<?php
//SPOTIFY API
header('Access-Control-Allow-Origin: *');
require_once('../config.php');
include './spotify-web-api/Request.php';
include './spotify-web-api/Session.php';
include './spotify-web-api/SpotifyWebAPI.php';
include './spotify-web-api/SpotifyWebAPIException.php';

$request = new SpotifyWebAPI\Request();
$session = new SpotifyWebAPI\Session(SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, SPOTIFY_REDIRECT_URI);
$api = new SpotifyWebAPI\SpotifyWebAPI();

$token = '';
//si je veux me deconnecter
if (isset($_GET['disconnect'])) {
	setcookie("spotify_token","",0,'/');
	echo "Vous avez été déconnecté de spotify";
	exit;
}
//si spotify m'envoi un token de connexion
elseif (isset($_GET['code'])) {
    $session->requestToken($_GET['code']);
    $token = $session->getAccessToken();
    setcookie("spotify_token",$token,0,'/');
    //je redirige vers le referer
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} 
//si je suis deja connecté à spotify
elseif(isset($_COOKIE["spotify_token"]) && !empty($_COOKIE["spotify_token"]) && $_COOKIE["spotify_token"] !== 'deleted' ){
    $token = $_COOKIE["spotify_token"];
}
//sinon je propose une connexion à spotify
else {
    header('Location: ' . $session->getAuthorizeUrl(array(
        'scope' => array('user-read-email', 'user-library-modify')
    )));
    exit;
}

try{
    //si une connexion est valide
    if($token!=''){
        $api->setAccessToken($token);
        
        if($_REQUEST['get_track_list']!=''){
            $data = $api->getUserPlaylistTracks($api->me()->id,$_REQUEST['get_track']);
            $reponse['content'] = $data;
            $reponse['result'] = 'success';
        }
        elseif($_REQUEST['custom']!=''){
            $data = $api->getCustomRequest($_REQUEST['custom']);
            $reponse['content'] = $data;
            $reponse['result'] = 'success';
        }
        //sinon on renvoi la liste des playlist = get_playlists
        else{
            $response = $api->getUserPlaylists($api->me()->id);
            $playlist = array();
            foreach($response->items as $pl){
                $cur_pl = array();
                $cur_pl['id'] = $pl->id;
                $cur_pl['name'] = $pl->name;
                $cur_pl['href'] = $pl->href;
                $cur_pl['tracks_num'] = $pl->tracks->total;
                $cur_pl['owner_id'] = $pl->owner->id;
                $playlist[] = $cur_pl;
            }
            
            $reponse['content'] = $playlist;
            $reponse['result'] = 'success';
        }
    }
}
catch(Exception $e){
    $msg = $e->getMessage();
    if( strpos($msg,'expired') !== false ){
        setcookie("spotify_token",'',0,'/');
        $reponse['content'] = "The access token expired";
        header('Location: ' . $session->getAuthorizeUrl(array(
            'scope' => array('user-read-email', 'user-library-modify')
        )));
        exit;
    }
    else{
        throw $e;
    }
}