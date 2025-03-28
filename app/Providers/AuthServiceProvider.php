<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\RedirectAfterLogin;
use App\Actions\Fortify\RedirectAfterLogout;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //  // Redirection après login
        // Fortify::loginResponse(function ($request) {
        //     return redirect()->intended('/map');
        // });

        // // Redirection après inscription
        // Fortify::registerResponse(function ($request) {
        //     return redirect('/map');
        // });

        // // Redirection après logout
        // Fortify::logoutResponse(function ($request) {
        //     return redirect('/login');
        // });
    }
}
