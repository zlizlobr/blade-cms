<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;

        // 1. Try to get tenant_id from session
        if ($request->session()->has('tenant_id')) {
            $tenantId = $request->session()->get('tenant_id');
        }

        // 2. If not in session and user is authenticated, get first tenant from user
        if (! $tenantId && auth()->check()) {
            $currentTenant = auth()->user()->currentTenant();
            if ($currentTenant) {
                $tenantId = $currentTenant->id;
                // Store in session for future requests
                $request->session()->put('tenant_id', $tenantId);
            }
        }

        // 3. Set tenant_id in application container for global access
        if ($tenantId) {
            app()->instance('tenant.id', $tenantId);
        }

        return $next($request);
    }
}
