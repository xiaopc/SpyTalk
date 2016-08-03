<?php
require_once './lib/medoo.min.php';
require_once './config.php';

Flight::map('bucketcheck', function(){
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
        $data = $query[0];
    } else {
       Flight::json(Array("status"=>"404","error"=>"Bucket not found."));
       return false;
    }
    if (!(empty($data["setting"]))) {
        $setting = json_decode($data["setting"],true);
    } else {
        Flight::json(Array("status"=>"200","data"=>"1"));
        return false;
    }
    if ($setting["isDelOnce"] == "true") {
        Flight::json(Array("status"=>"200","data"=>Array("DelOnce"=>"true") ));
    } else {
        Flight::json(Array("status"=>"200","data"=>Array("DelOnce"=>"false")));
    }
    return false;
});

?>