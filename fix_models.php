<?php
$files = ['app/Models/Departemen.php', 'app/Models/Role.php'];
foreach($files as $file) {
    $c = file_get_contents($file);
    $c = str_replace('use HasFactory, HasUuids;', 'use HasFactory;', $c);
    $c = str_replace("use Illuminate\\Database\\Eloquent\\Concerns\\HasUuids;\n", '', $c);
    $c = str_replace("use Illuminate\\Database\\Eloquent\\Concerns\\HasUuids;\r\n", '', $c);
    file_put_contents($file, $c);
}
echo "Models fixed.\n";
