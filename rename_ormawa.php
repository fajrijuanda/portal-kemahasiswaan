<?php
$files = [
    'app/Http/Controllers/UnitActivityController.php',
    'app/Http/Controllers/OrmawaAdminController.php',
    'app/Http/Controllers/DashboardController.php',
    'tests/Feature/MvpRevisionTest.php',
    'routes/web.php',
    'resources/views/dashboard/rekap.blade.php',
    'resources/views/unit-activities/index.blade.php',
    'resources/views/dashboard/index.blade.php',
    'resources/views/layouts/navigation.blade.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Rename route name
        $content = str_replace("route('ormawa-admin.index'", "route('ormawa.index'", $content);
        $content = str_replace("name('ormawa-admin.index')", "name('ormawa.index')", $content);
        
        // Update URL
        $content = str_replace("'/ormawa-admin/{section?}'", "'/ormawa-data/{section?}'", $content);
        $content = str_replace("request()->is('ormawa-admin*')", "request()->is('ormawa-data*')", $content);
        
        file_put_contents($path, $content);
        echo "Updated $file\n";
    }
}
