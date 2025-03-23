<?php

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/../config/join-me-project-firebase-adminsdk-dixk6-fe19b026f7.json') // เส้นทางไปยังไฟล์ JSON
    ->withDatabaseUri('https://join-me-project-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
$auth = $factory->createAuth();

?>