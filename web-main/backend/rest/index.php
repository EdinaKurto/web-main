<?php

require "../vendor/autoload.php";
require "./services/ExamService.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::register('examService', 'ExamService');

require 'routes/ExamRoutes.php';

Flight::start();
