<?php

$dirs = [
    __DIR__ . '/app/Http/Controllers/SmartJabar',
    __DIR__ . '/app/Http/Controllers/Sidebar',
    __DIR__ . '/app/Http/Controllers/SadaJabar',
    __DIR__ . '/app/Http/Controllers/Rekayasa',
    __DIR__ . '/app/Http/Controllers/Intop',
    __DIR__ . '/app/Http/Controllers'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = scandir($dir);
    foreach ($files as $f) {
        if (pathinfo($f, PATHINFO_EXTENSION) === 'php') {
            $path = $dir . '/' . $f;
            $content = file_get_contents($path);
            
            // views with compact
            $content = preg_replace('/return\s+view\(\s*\'[^\']+\'\s*,\s*compact\((.*?)\)\s*\);/s', 'return response()->json(compact($1));', $content);
            // views without compact
            $content = preg_replace('/return\s+view\(\s*\'[^\']+\'\s*\);/s', 'return response()->json([\'message\' => \'Success\']);', $content);
            
            // redirects with success message
            $content = preg_replace('/return\s+redirect\(\)->(?:route\(\s*\'[^\']+\'\s*\)|back\(\))\s*->with\(\s*\'[^\']+\'\s*,\s*(.*?)\s*\);/s', 'return response()->json([\'message\' => $1]);', $content);
            // redirects without success message
            $content = preg_replace('/return\s+redirect\(\)->(?:route\(\s*\'[^\']+\'\s*\)|back\(\))\s*;/s', 'return response()->json([\'message\' => \'Success\']);', $content);
            
            file_put_contents($path, $content);
        }
    }
}
echo "Done feature controllers.\n";
