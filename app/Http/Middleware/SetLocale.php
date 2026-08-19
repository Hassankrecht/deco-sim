<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin*')) {
            $adminLocale = session('admin_locale', config('app.admin_locale', 'pt'));
            $allowed = config('app.admin_locales', ['pt', 'en']);

            if (! in_array($adminLocale, $allowed)) {
                $adminLocale = config('app.admin_locale', 'pt');
            }

            App::setLocale($adminLocale);
            return $next($request);
        }

        $locale = session('app_locale', config('app.locale'));

        if (in_array($locale, config('app.frontend_locales', ['pt', 'en']))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
