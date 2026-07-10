<?php

// Bootstrap Laravel properly
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

$pass = 0;
$fail = 0;

function check(string $label, bool $result): void {
    global $pass, $fail;
    if ($result) {
        echo "  PASS: {$label}" . PHP_EOL;
        $pass++;
    } else {
        echo "  FAIL: {$label}" . PHP_EOL;
        $fail++;
    }
}

echo PHP_EOL . "=== Phase 4 Automated Verification ===" . PHP_EOL . PHP_EOL;

// 1. Verify Views Exist
check('layouts.app exists', View::exists('layouts.app'));
check('partials.sidebar exists', View::exists('partials.sidebar'));
check('partials.navbar exists', View::exists('partials.navbar'));
check('partials.footer exists', View::exists('partials.footer'));
check('dashboard.index exists', View::exists('dashboard.index'));

// 2. Verify View Renders Without Error (Authenticated)
$user = User::where('email', 'doctor@example.com')->first();
if ($user) {
    Auth::login($user);
    try {
        $viewContent = view('dashboard.index')->render();
        check('Dashboard renders successfully', !empty($viewContent));
        check('Dashboard contains sidebar', str_contains($viewContent, 'id="sidebar"'));
        check('Dashboard contains navbar', str_contains($viewContent, 'class="navbar"'));
        check('Dashboard contains footer', str_contains($viewContent, 'footer'));
        check('Dashboard contains Total Patients card', str_contains($viewContent, 'Total Patients'));
        check('Dashboard contains placeholder 0', str_contains($viewContent, '0'));
    } catch (\Exception $e) {
        check('Dashboard renders successfully: ' . $e->getMessage(), false);
    }
} else {
    check('Demo user found for testing', false);
}

// 3. CSS exists
check('dashboard.css exists', file_exists(public_path('css/dashboard.css')));

echo PHP_EOL;
echo "=== Results: {$pass} passed, {$fail} failed ===" . PHP_EOL . PHP_EOL;
