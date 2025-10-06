<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;

//Artisan::call('config:clear');
//Artisan::call('cache:clear');
//Artisan::call('route:clear');
//Artisan::call('view:clear');

//exit;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas do admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Rotas de autenticação
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Rotas protegidas do admin
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Sections
        Route::get('/forms/bannerSection', [FormController::class, 'bannerSection'])->name('forms.bannerSection');
        Route::post('/forms/bannerSection', [FormController::class, 'storeBannerSection'])->name('forms.bannerSection.store');

        Route::get('/forms/spotifySection', [FormController::class, 'spotifySection'])->name('forms.spotifySection');
        Route::post('/forms/spotifySection', [FormController::class, 'storeSpotifySection'])->name('forms.spotifySection.store');
        
        Route::get('/forms/contactPage', [FormController::class, 'contactPage'])->name('forms.contactPage');

        // Posts
        Route::resource('posts', PostController::class);

        Route::resource('categories', CategoryController::class)->except(['show']);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');

        // Phpinfo
        Route::get('/phpinfo', function () {
            phpinfo();
        });
    });
});

// Rotas do site
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('news.show');
// ADICIONADO: Nova rota para listagem de notícias por categoria/editoria
Route::get('/noticias/editorias/{slug}', [NewsController::class, 'showByCategory'])->name('news.category');
Route::get('/contato', [ContactController::class, 'index'])->name('contact');

// Rotas para redes sociais (redirecionamentos externos)
Route::get('/youtube', function () {
    return redirect()->away('https://www.youtube.com/@BandaCorpoeAlma');
})->name('social.youtube');

Route::get('/spotify', function () {
    return redirect()->away('https://open.spotify.com/artist/0SzWnIde8xRsuGDZYZdvE5?si=AQy5QS9NSIW-8so3ZUkzBA');
})->name('social.spotify');

Route::get('/instagram', function () {
    return redirect()->away('https://www.instagram.com/corpoealmabr');
})->name('social.instagram');

Route::get('/facebook', function () {
    return redirect()->away('https://www.facebook.com/corpoealmabr');
})->name('social.facebook');

Route::get('/tiktok', function () {
    return redirect()->away('https://www.tiktok.com/@corpoealmaofc?_t=ZM-90KbqCRlN2e&_r=1');
})->name('social.tiktok');

Route::get('/pinterest', function () {
    return redirect()->away('https://pin.it/6ITVGg7HB');
})->name('social.pinterest');