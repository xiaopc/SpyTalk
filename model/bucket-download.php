<?php
require_once './lib/medoo.min.php';
require_once './config.php';

Flight::map('bucketdownload', function(){
    $bucket = Flight::request()->data->bucket;
    if  (empty($bucket))  {
        Flight::json(Array("status"=>"400","error"=>"Missing Necessary Parameters."));
        return false;
    }
    $database = new medoo(Flight::get('dbconfig'));
    $query = $database->select("content", "*", [
        "bucket" => $bucket
    ]);
    if (count($query) >= 1) {
        $setting = json_decode($query[0]["setting"],true);
        switch ($setting["isIPcheck"]){
            case "cn":
                if ( (Flight::ipip()["status"] == "500") or (Flight::ipip()["data"]["0"] != "中国") ) {Flight::json(Array("status"=>"403","error"=>Flight::ipip()["data"]["0"]." IP was restricted."));return false;}
                break;
            case "us":
                if ( (Flight::ipip()["status"] == "500") or (Flight::ipip()["data"]["0"] != "美国") ) {Flight::json(Array("status"=>"403","error"=>Flight::ipip()["data"]["0"]." IP was restricted."));return false;}
                break;
            case "false":
                break;
            default:
                // Warning!!! 1.x32 system may cause negative number 2.IPv4 only
                if ( $setting["isIPcheck"] != ip2long(Flight::request()->ip) ) {Flight::json(Array("status"=>"403","error"=>"IP was restricted."));return false;}
        }
        if ($setting["isDelOnce"] == "true"){
             $querydel = $database->delete("content", ["id"=>$query[0]["id"]]);
        }
        Flight::json(Array("status"=>"200","data"=>$query[0],"error"=>"null"));
    } else {
        Flight::json(Array("status"=>"404","error"=>"Bucket not found."));
    } 
    return false;
});

?>