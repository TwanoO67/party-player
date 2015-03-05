<?php
    header('Access-Control-Allow-Origin: *');
	/*
    	FONCTIONS
	*/
	
	function compareItems($a, $b) {
		//si les 2 chansons sont parmis les lu / nouvelle, on les trie par vote
	    if ($a['alreadyRead'] === false && $b['alreadyRead'] === false) {
    	    //si ils ont le meme resultat de vote on trie par data
    	    if($a['vote'] == $b['vote']){
        	    return ($a['addTime'] < $b['addTime']) ? -1 : 1;
    	    }
    	    else
	            return ($a['vote'] > $b['vote']) ? -1 : 1;
	    }
	    //sinon on les trie par lecture
	    else{
    	    return ($a['alreadyRead'] < $b['alreadyRead']) ? -1 : 1;
	    }
	}
	
	function triItemsByVote($data){
    	//re-ordonne le tableau aprés les votes
        usort($data['items'], 'compareItems');//compare par lecture et vote
        //arsort($data['items']);//re-ordonne en fonction de la place obtenue
        //$data['items'] = array_reverse($data['items']);//trie par ordre décroissant
        
        $data['lastUpdateTime'] = time();
        return $data;
	}
	
	function ecritureData($data,$time=true){
        global $filename;
        if($time){
    	    $data['lastUpdateTime'] = time();
    	}
        return file_put_contents($filename, json_encode($data));
    }
    
    function getLastReadID($data){
        $lastRead = 1;
        $result = 0;
        foreach($data['items'] as $item){
            if($item['alreadyRead'] > $lastRead){
                $lastRead = $item['alreadyRead'];
                $result = $item['id'];
            }
        }
        return $result;
    }
	
	
	/*
    	
    	DEBUT SCRIPT
    	
	*/
	
    $reponse = array();
    $reponse['result'] = 'error';
    
    if( isset($_REQUEST['sessid']) )
    {
        $id = strtoupper($_REQUEST['sessid']);
        $filename = '_playlists/'.$id.'.json';
        
        if( isset($_REQUEST['mode']) ){
            $mode = $_REQUEST['mode'];
            
            //fournie: mode=create,sessid
            //recu: result,content
            if($mode == 'create'){
                //creation du fichier de playlist
                $data = array(
	                'lastUpdateTime' => time(),
	                'creationTime' => time(),
	                'items' => array()
                );
                if(ecritureData($data)){
                    $reponse['result'] = 'success';
                    $reponse['content'] = 'Le fichier a été crée';
                }
                else{
                    $reponse['error'] = "Erreur d'écriture!";
                }
            }
            else{
	            //recuperation des données existante
                $donnee = '';
                $data_ready = false;
                if(file_exists($filename)){
                	$donnee = file_get_contents($filename);
                }
                
                if($donnee != ''){
                    $data = json_decode($donnee,true);
	                if(is_array($data) && is_array($data['items'])){
    	                $data_ready = true;
    	            }
    	        }
            }
            
            
            if(!$data_ready){
                $reponse['error'] = "no_file";
            }
            //fournie: mode=add,sessid,id,user
            //recu: result,content
            elseif($mode == 'add'){
                //ajout d'un item dans le fichier
                if(empty($_REQUEST['id'])){
	                $reponse['error'] = "Pas d'id fournie!";
                }
                elseif(empty($_REQUEST['user'])){
	                $reponse['error'] = "Pas de user fournie!";
                }
                else{
	                $item_exist = false;
	                foreach($data['items'] as $item){
		                if($item['id'] == $_REQUEST['id']){
			                $item_exist = true;
		                }
	                }
	                
	                if($item_exist){
		                $reponse['error'] = "Cette vidéo est déjà dans la liste...";
	                }
	                else{
		                $new_item = array();
		                $new_item['id'] = $_REQUEST['id'];
		                $new_item['addUser'] = $_REQUEST['user'];
		                $new_item['addTime'] = time();
		                $new_item['vote'] = 0;
		                $new_item['votes'] = array();
		                $new_item['alreadyRead'] = false;
		                
	                    $data['items'][] = $new_item;
	                    
	                    $data = triItemsByVote( $data );
	                    
	                    if(ecritureData($data)){
	                        $reponse['result'] = 'success';
	                    }
	                    else{
	                        $reponse['error'] = "Erreur d'écriture!";
	                    }
	                }
                }
            }
            //fournie: mode=add,sessid,id,user
            //recu: result,content
            elseif($mode == 'delete'){
                //ajout d'un item dans le fichier
                if(empty($_REQUEST['id'])){
	                $reponse['error'] = "Pas d'id fournie!";
                }
                elseif(empty($_REQUEST['user'])){
	                $reponse['error'] = "Pas de user fournie!";
                }
                else{
	                $item_found = false;
	                foreach($data['items'] as $index => $item){
		                if($item['id'] == $_REQUEST['id']){
			                unset($data['items'][$index]);
			                $item_found = true;
		                }
	                }
	                
	                if(!$item_found){
    	                $reponse['result'] = 'item non trouvé';
    	            }
    	            elseif(ecritureData($data)){
                        $reponse['result'] = 'success';
                    }
                    else{
                        $reponse['error'] = "Erreur d'écriture!";
                    }
                }
            }
            //fournie: mode=read,sessid,lastUpdateTime
            //recu: result,content
            elseif($mode == 'read'){
                //les données n'ont pas été mis à jours depuis le dernier read
                if(isset($data['lastUpdateTime']) && ($_REQUEST['lastUpdateTime'] >= $data['lastUpdateTime'])){
	                $reponse['content'] = 'no_news';
                }
                else{
                    $data['lastReadID'] = getLastReadID($data);
	                $reponse['content'] = $data;
                }
                $reponse['result'] = 'success';
            }
            //fournie: mode=vote,user,item_id,vote
            //recu: result
            elseif($mode == 'vote'){
                
                if(empty($_REQUEST['user']) || empty($_REQUEST['item_id']) || empty($_REQUEST['vote']) ){
                    $reponse['error'] = "Données incomplètes pour voter!";
                }
                else{
                    $user = $_REQUEST['user'];
                    $id = $_REQUEST['item_id'];
                    $vote = $_REQUEST['vote'];
                    $vote_token = $_REQUEST['vote_token'];
                    
                    $found = false;
                    $alreadyVoted = false;
                    
                    foreach($data['items'] as &$item){
	                	if(  $item['id'] == $id){
    	                	
    	                	//check si deja voté
                            foreach($item['votes'] as $vote){
                                if($vote['user'] == $user || $vpte['token'] == $vote_token){
                                    $alreadyVoted = true;
                                }
                            }
                            
                            if(!$alreadyVoted){
	                            $value = $_REQUEST['vote'];
    		                	if($value == 'moins'){
    			                	$item['vote'] -= 1;
    		                	}
    		                	else{
    			                	$item['vote'] += 1;
    			                	$value = 'plus';
    		                	}
    		                	$vote = array(
        		                	/*'item_id' => $id,*/
        		                	'token' => $vote_token,
    			                	'user' => $user,
    			                	'value' => $value
    		                	);
    		                	$item['votes'][] = $vote;
    		                	
    		                	$found = true;
		                	}
		                	else{
            	                $reponse['error'] = "Vous avez déjà voté pour cette chanson!";
        	                }
	                	}
	                }
	                
	                if($found){
		                $data = triItemsByVote($data);
		                
		                if(ecritureData($data)){
	                        $reponse['result'] = 'success';
	                    }
	                    else{
	                        $reponse['error'] = "Erreur d'écriture!";
	                    }
	                }
	                else{
		                $reponse['error'] = "ID non existant";
	                }
	                
                   
                } 
            }
            elseif($mode == 'next_track'){
                
                    $last = $_REQUEST['last_played'];
                    $next_track = 0;
                    //si on a deja une chanson, on passe à la suivante
                    $found = false;
                    if($last != '' && $last > 0){
	                    $found_last = false;
	                    foreach($data['items'] as &$item){
		                    //on cherche l'ancien id
		                	if( $item['id'] == $last ){
			                	if($item['alreadyRead'] === false){
			                	    $item['alreadyRead'] = (time())-1;
			                	}
			                	$found_last = true;
			                	continue;//on veut le suivant
		                	}
		                	//on trouve le suivant non-lu
		                	if($found_last && !$item['alreadyRead']){
			                	$next_track = $item['id'];
			                	$found=true;
			                	break;
		                	}
		                }
                    }
                    
                    //sinon on envoi le premier non-lu
                    if(!$found){
                        foreach($data['items'] as &$item){
                            if(!$item['alreadyRead']){
			                	$next_track = $item['id'];
			                	break;
			                }
                        }
                    }
                    
                    if( count($data['items'])==0 ){
	                    $reponse['error'] = "no_data";
                    }elseif($next_track === 0){
	                    $reponse['error'] = "Plus de chanson à lire. ";   
	                    ecritureData($data,false);
                    }
                    else{
	                    $reponse['content'] = $next_track;
                        if(ecritureData($data)){
                            $reponse['result'] = 'success';
                        }
                        else{
    	                    $reponse['error'] = "erreur de sauvegarde du fichier";
                        }
                    }
                    
                    
            }
            //fournie: mode=add,sessid,id,user
            //recu: result,content
            elseif($mode == 'mark_read'){
                //ajout d'un item dans le fichier
                if(empty($_REQUEST['id'])){
	                $reponse['error'] = "Pas d'id fournie!";
                }
                else{
	                $item_found = false;
	                foreach($data['items'] as &$item){
		                if($item['id'] == $_REQUEST['id']){
			                $item['alreadyRead'] = time();
			                $item_found = true;
		                }
	                }
	                
	                $data = triItemsByVote($data);
	                
	                if(!$item_found){
    	                $reponse['result'] = 'item non trouvé';
    	            }
    	            elseif(ecritureData($data)){
                        $reponse['result'] = 'success';
                    }
                    else{
                        $reponse['error'] = "Erreur d'écriture!";
                    }
                }
            }
            elseif($mode == 'unread_all'){
                    foreach($data['items'] as &$item){
	                    $item['alreadyRead'] = false;
    	            }
    	            $data = triItemsByVote($data);
                    
                    if(ecritureData($data)){
                        $reponse['result'] = 'success';
                    }
                    else{
	                    $reponse['error'] = "erreur de sauvegarde du fichier";
                    }
            }
            elseif($mode == 'last_read'){
                //ajout d'un item dans le fichier
                
	                $id = getLastReadID($data);
	                	                
	                if($id > 0){
                        $reponse['result'] = 'success';
                    }
                    else{
                        $reponse['error'] = 'no_read';
                    }
                
            }
            
        }
        else{
            $reponse['error'] = "Pas de mode selectionné!";
        }
        
    }
    else{
        $reponse['error'] = "Numéro de session invalide!";
    }
    echo json_encode($reponse);
    exit;
?>