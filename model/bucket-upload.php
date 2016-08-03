<?php
require_once './lib/medoo.min.php';
require_once './config.php';
require_once './lib/recaptcha/autoload.php';


Flight::map('bucketupload', function(){
    $bucket = Flight::request()->data->bucket;
    $method = Flight::request()->data->method;
    $isDelOnce = Flight::request()->data->isDelOnce;
    $isIPcheck = Flight::request()->data->isIPcheck;
    $encrypted = Flight::request()->data->encrypted;
    if ( (empty($bucket)) or (empty($method)) or (empty($encrypted)) or (empty($isIPcheck))) {
        Flight::json(Array("status"=>"400","error"=>"Missing Necessary Parameters."));
        return false;
    }
    $recaptcha = new \ReCaptcha\ReCaptcha(Flight::get('reCaptchaSecretkey'));
    $resp = $recaptcha->verify(Flight::request()->data->gRecaptchaResponse, Flight::request()->ip);
    if (!($resp->isSuccess())) {
        Flight::json(Array("status"=>"412","error"=>"reCaptcha Failed."));
        return false;
    }
    if ($isIPcheck == "current") {
        $isIPcheck = ip2long(Flight::request()->ip); // Warning!!! 1.x32 system may cause negative number 2.IPv4 only
    }
    $setting = array ('isDelOnce'=>$isDelOnce,'isIPcheck'=>$isIPcheck);
    $database = new medoo(Flight::get('dbconfig'));
    $query = $database->insert("content", [
        "bucket" => $bucket,
        "method" => $method,
    	"setting" => json_encode($setting),
    	"encrypted" => $encrypted
    ]);
    if ((integer)$query>=1) {
       Flight::json(Array("status"=>"200","data"=>["bucket"=>$bucket],"error"=>"null"));
    } else {
       Flight::json(Array("status"=>"500","error"=>"Insert data failed."));
    } 
    return false;
});

?>