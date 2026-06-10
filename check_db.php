<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doctors = \App\Models\User::where('role', 'dokter')->get(['name', 'email']);
foreach($doctors as $d) {
    echo $d->name . " | " . $d->email . "\n";
}
