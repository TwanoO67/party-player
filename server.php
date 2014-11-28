<?php
    
    $reponse = array();
    $reponse['result'] = 'error';
    
    if( isset($_REQUEST['sessid']) )
    {
        $id = $_REQUEST['sessid'];
        $filename = 'json/playlist_'.$id.'.json';
        
        if( isset($_REQUEST['mode']) ){
            $mode = $_REQUEST['mode'];
            
            if($mode == 'write'){
                //écriture de tous le fichier tel que fournie
                $data = $_REQUEST['playlist'];
                if($data != ''){
                    if(file_put_contents($filename, json_encode($data) )){
                        $reponse['result'] = 'success';
                    }
                    else{
                        $reponse['error'] = "Erreur d'écriture!";
                    }
                }
                else{
                    $reponse['error'] = "Pas de data à écrire!";
                }
            }
            elseif($mode == 'add'){
                //ajout d'une ligne dans le fichier
                $item = $_REQUEST['item'];
                if($item != ''){
                    $donnee = file_get_contents($filename);
                    $data = json_decode($donnee,true);
                    if(is_array($data)){
                        $index = $item['id'];
                        $data[$index] = $item;
                        if(file_put_contents($filename, json_encode($data))){
                            $reponse['result'] = 'success';
                        }
                        else{
                            $reponse['error'] = "Erreur d'écriture!";
                        }
                    }
                    else{
                        $reponse['error'] = "Pas de data existante!";
                    }
                }
            }
            elseif($mode == 'read'){
                //recuperation du contenu
                $donnee = file_get_contents($filename);
                if($donnee != ''){
                    $reponse['content'] = $donnee;
                    $reponse['result'] = 'success';
                }
            }
            elseif($mode == 'vote'){
                
                if(!isset($_REQUEST['user']) || !isset($_REQUEST['youtube_id']) || !isset($_REQUEST['vote']) ){
                    $reponse['error'] = "Données incompletes pour voter!";
                }
                else{
                    $user = $_REQUEST['user'];
                    $song = $_REQUEST['youtube_id'];
                    $vote = $_REQUEST['vote'];
                    
                    //recuperation du contenu
                    $donnee = file_get_contents($filename);
                    $data = json_decode($donnee,true);
                    
                    
                    
                    if( is_array($data) ){
                        
                        if(isset($data[$song])){
                            if(!isset($data['votant'])){
                                $data[$song]['votant'] = array();
                            }
                            
                            if(isset($data[$song]['votant'][$user]) ){
                                $reponse['error'] = "L'utilisateur $user a déja voté!";
                            } 
                            else{
                                 
                                
                                if($vote == 'plus'){
                                    $data[$song]['vote'] += 1; 
                                }
                                elseif($vote == 'moins'){
                                    $data[$song]['vote'] -= 1; 
                                }
                                
                                $data[$song]['votant'][$user] = $vote;
                                
                                if(file_put_contents($filename, json_encode($data))){
                                    $reponse['result'] = 'success';
                                }
                                else{
                                    $reponse['error'] = "Erreur d'écriture!";
                                }
                            }
                            
                        }
                        else{
                            $reponse['error'] = "ID Youtube inexistant!";
                            $reponse['content'] = $data;
                        } 
                    }
                    else{
                        $reponse['error'] = "Pas de data existante!";
                    }
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