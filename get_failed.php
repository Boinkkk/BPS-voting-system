<?php

require 'vendor/autoload.php';
require_once 'bootstrap/app.php';
app('Illuminate\Contracts\Console\Kernel')->bootstrap();
$f = DB::table('failed_jobs')->orderBy('id', 'desc')->first();
if ($f) {
    $lines = explode("\n", $f->exception);
    for ($i = 0; $i < min(15, count($lines)); $i++) {
        echo $lines[$i]."\n";
    }
} else {
    echo 'No failed jobs';
}
