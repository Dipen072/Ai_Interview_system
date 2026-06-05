<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ai = App\Services\AiServiceFactory::make();
print_r($ai->evaluateAnswer('Q', 'A'));
