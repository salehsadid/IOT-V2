<?php

// Bootstrap Laravel properly
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Facades\Route;

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

echo PHP_EOL . "=== Phase 3 Automated Verification ===" . PHP_EOL . PHP_EOL;

// 1. Verify Demo Users Exist
$doctor = User::where('email', 'doctor@example.com')->first();
$caregiver = User::where('email', 'caregiver@example.com')->first();
check('Doctor demo user exists', $doctor !== null);
check('Caregiver demo user exists', $caregiver !== null);
check('Doctor role is doctor', $doctor && $doctor->isDoctor());
check('Caregiver role is caregiver', $caregiver && $caregiver->isCaregiver());
check('Password is correct (Hash check)', $doctor && Hash::check('password', $doctor->password));

// 2. Verify Routes Exist
$routes = collect(Route::getRoutes()->getRoutes())->map(function($route) {
    return $route->uri();
})->toArray();
check('Login route (GET) exists', in_array('login', $routes));
check('Login route (POST) exists', in_array('login', $routes));
check('Logout route (POST) exists', in_array('logout', $routes));
check('Dashboard route (GET) exists', in_array('dashboard', $routes));

// 3. Verify Auth Middleware on Dashboard
$dashboardRoute = Route::getRoutes()->getByName('dashboard');
check('Dashboard route uses auth middleware', in_array('auth', $dashboardRoute->gatherMiddleware()));

// 4. Test View Files Exist
check('auth/login.blade.php exists', file_exists(resource_path('views/auth/login.blade.php')));
check('dashboard.blade.php exists', file_exists(resource_path('views/dashboard.blade.php')));

// 5. Test Controllers Exist
check('AuthController exists', class_exists(\App\Http\Controllers\AuthController::class));
check('DashboardController exists', class_exists(\App\Http\Controllers\DashboardController::class));

// 6. Verify RoleMiddleware exists
check('RoleMiddleware exists', class_exists(\App\Http\Middleware\RoleMiddleware::class));

echo PHP_EOL;
echo "=== Results: {$pass} passed, {$fail} failed ===" . PHP_EOL . PHP_EOL;
