<?php

use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(ChatController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('chat', 'index')->name('chat.index');
    Route::get('ajax/users/{id}/messages', 'show')->name('ajax.messages.get');
    Route::post('ajax/message/send', 'store')->name('ajax.chat.store');
});

Route::get('/send', function () {
    $message = \App\Models\Message::create([
        'user_id' => 2,
        'message' => 'Hello aliya'
    ]);
    \App\Events\MessageEvent::dispatch(\Illuminate\Support\Facades\Auth::user(), $message);
});




require __DIR__.'/auth.php';
