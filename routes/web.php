<?php
use App\Mail\HelloMail;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
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
use Illuminate\Support\Facades\DB;


// Route::get('/test-db', function () {
//     try {
//         DB::connection()->getPdo();
//         return "✅ Laravel đã kết nối được với Neon PostgreSQL!";
//     } catch (\Exception $e) {
//         return "❌ Lỗi kết nối: " . $e->getMessage();
//     }
// });

Route::get('/', function () {
    // Mail::to('thuoanhnh@gmail.com')
    return view('welcome');
    // ->send(new ForgotPasswordMail());
   // ->send(new HelloMail());
});
