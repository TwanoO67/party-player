<?php
function curl_download($url,$referer="",$useragent="",$header=0,$postParam=array()){
     
        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }

        $ch = curl_init();

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set a referer
        if($referer!="")
            curl_setopt($ch, CURLOPT_REFERER, $referer);

        // User agent
        if($useragent!="")
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, $header);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //Si on veut envoyer en POST
        if(count($postParam) > 0){
            /*On indique à curl d'envoyer une requete post*/
            curl_setopt($ch, CURLOPT_POST,true);
            /*On donne les paramêtre de la requete post*/
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postParam);
        }

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }
    
    $curl_data = curl_download("http://597901:p1liO9XfaHQHHBQRJeKv@api.pleer.com/token.php","","",1,array(
        'grant_type' => 'client_credentials',
        '597901'=>'p1liO9XfaHQHHBQRJeKv'
    ));
    $tab = explode('{"access_token":"',$curl_data);
    $tab2 = explode('","expires_in"',$tab[1]);
    
    $token = $tab2[0];
    
    
    
?>