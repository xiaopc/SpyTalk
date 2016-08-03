<?php
require_once 'lib/flight/Flight.php';
require_once 'model/bucket-upload.php';
require_once 'model/bucket-download.php';
require_once 'model/bucket-check.php';
require_once 'model/ip.php';

Flight::route('/', function(){
    include __dir__.'/viewmodel/index.html';
});

Flight::route('/api/*', function(){
    header('Content-type: application/json');
    return true;
});

Flight::route('/api/file/up', function(){
    Flight::bucketupload();
    return false;
});
Flight::route('/api/file/dl', function(){
    Flight::bucketdownload();
    return false;
});
Flight::route('/api/file/check', function(){
    Flight::bucketcheck();
    return false;
});

Flight::start();
?>