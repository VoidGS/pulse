<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Inertia\Response;

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
        JsonResource::withoutWrapping();

        Model::preventLazyLoading();

        RedirectResponse::macro('toast', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'default',
                'toast' => $message,
            ]);
        });

        RedirectResponse::macro('toastSuccess', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'success',
                'toast' => $message,
            ]);
        });

        RedirectResponse::macro('toastDanger', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'danger',
                'toast' => $message,
            ]);
        });

        Response::macro('toast', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'default',
                'toast' => $message,
            ]);
        });

        Response::macro('toastSuccess', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'success',
                'toast' => $message,
            ]);
        });

        Response::macro('toastDanger', function ($message) {
            return $this->with('flash', [
                'toastStyle' => 'danger',
                'toast' => $message,
            ]);
        });
    }
}
