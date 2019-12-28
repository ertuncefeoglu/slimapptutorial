<?php

use Slim\App;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new App;

require "../src/routes/courses.php";

$app->run();