<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$validator = \Illuminate\Support\Facades\Validator::make(
    ['items' => [['name' => '', 'qty' => 1, 'price' => '']]],
    [
        'items.*.name' => 'required',
        'items.*.qty' => 'required',
        'items.*.price' => 'required'
    ]
);

echo json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT);
