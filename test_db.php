<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$spds = \App\Models\Spd::all();
echo $spds->toJson(JSON_PRETTY_PRINT);
