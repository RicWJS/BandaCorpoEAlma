<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination.bootstrap-5');
        Paginator::defaultSimpleView('pagination.simple-bootstrap-5');

        Carbon::setLocale(config('app.locale'));

        // MODIFICADO: Agora este composer atende a um array de views.
        View::composer(['includes.footer', 'partials._post-sidebar'], function ($view) {
            // A consulta é feita uma única vez aqui.
            $categories = Category::withCount(['posts' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->having('posts_count', '>', 0)
                ->orderBy('name', 'asc')
                ->get();
            
            // A variável 'categories' agora está disponível em ambas as views.
            $view->with('categories', $categories);
        });
    }
}
