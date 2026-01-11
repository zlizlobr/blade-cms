<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        // Set the application locale
        App::setLocale($locale);

        // Store in session if not already set
        if (! $request->session()->has('locale')) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }

    /**
     * Detect the locale from various sources.
     */
    protected function detectLocale(Request $request): string
    {
        $supportedLocales = config('i18n.supported_locales', ['cs', 'en']);
        $fallbackLocale = config('i18n.fallback_locale', 'cs');

        // 1. Try cookie
        if ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
            if (in_array($locale, $supportedLocales)) {
                return $locale;
            }
        }

        // 2. Try session
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
            if (in_array($locale, $supportedLocales)) {
                return $locale;
            }
        }

        // 3. Try Accept-Language header
        $locale = $request->getPreferredLanguage($supportedLocales);
        if ($locale && in_array($locale, $supportedLocales)) {
            return $locale;
        }

        // 4. Fallback
        return $fallbackLocale;
    }
}
