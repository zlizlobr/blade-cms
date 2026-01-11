<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    /**
     * Change the application locale.
     */
    public function change(Request $request, string $locale): RedirectResponse
    {
        $supportedLocales = config('i18n.supported_locales', ['cs', 'en']);

        // Validate locale
        if (! in_array($locale, $supportedLocales)) {
            abort(400, 'Unsupported locale');
        }

        // Set session
        $request->session()->put('locale', $locale);

        // Set cookie (expires in 1 year)
        Cookie::queue('locale', $locale, 525600);

        return redirect()->back();
    }
}
