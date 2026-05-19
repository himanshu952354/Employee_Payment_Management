<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$s = new \App\Services\MongoDBService();
$c = $s->selectCollection("users");
print_r($c->find());
