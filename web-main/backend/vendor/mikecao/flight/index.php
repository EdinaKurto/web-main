<?php
require __DIR__ . '/../vendor/autoload.php';

require 'flight/Flight.php';

Flight::route('/', function () {
    echo 'hello world!';
});

Flight::start();
