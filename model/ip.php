<?php

Flight::map('ipip', function(){
    $req = curl_init();  
    curl_setopt($req, CURLOPT_URL, "http://freeapi.ipip.net/".Flight::request()->ip);  
    curl_setopt($req, CURLOPT_HEADER, false);  
    curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($req, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($req, CURLOPT_TIMEOUT, 1);
    curl_setopt($req, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','User-Agent:Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36'));  
    $result=curl_exec($req);
    curl_close($req);
    if ($result == false){return(Array("status"=>"500","error"=>"ipip Failed."));} else {
        return(Array("status"=>"200","data"=>json_decode($result,"true")));
    }
});

?>