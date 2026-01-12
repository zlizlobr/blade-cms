# Blade CMS Views Documentation

## Directory Structure

```
resources/views/
├── admin/                      # Admin area (dashboard, management)
│   ├── layouts/
│   │   └── admin.blade.php    # Admin layout with sidebar
│   ├── partials/
│   │   └── admin-sidebar.blade.php
│   ├── components/             # Admin-specific components
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── submissions/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── profile/
│   │   ├── edit.blade.php
│   │   └── partials/
│   └── users/
├── public/                     # Public website
│   ├── layouts/
│   │   └── main.blade.php     # Main public layout
│   ├── partials/
│   │   ├── header.blade.php
│   │   ├── footer.blade.php
│   │   └── navigation.blade.php
│   ├── components/             # Public-specific components
│   └── pages/
│       └── home.blade.php
├── auth/                       # Authentication views
│   ├── layouts/
│   │   └── guest.blade.php    # Auth layout
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   ├── verify-email.blade.php
│   └── confirm-password.blade.php
├── components/                 # Global shared components
│   ├── application-logo.blade.php
│   ├── input-error.blade.php
│   ├── input-label.blade.php
│   ├── text-input.blade.php
│   ├── primary-button.blade.php
│   └── ...
└── themes/                     # Legacy theme system (preserved)
    └── default/
        └── layouts/
            ├── app.blade.php
            └── welcome.blade.php
```

## View Namespace System

The application uses Laravel's view namespace feature for clear separation of concerns:

**Registered namespaces:**
- `admin::` → `resources/views/admin/`
- `public::` → `resources/views/public/`
- `auth.` → `resources/views/auth/` (no namespace, default Laravel behavior)

**Configured in:**
```
app/Infrastructure/Providers/ViewServiceProvider.php
```

## Creating Views

### Admin Views

Admin views use the `admin::` namespace and extend the admin layout with sidebar.

**Example:**
```blade
{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin::layouts.admin')

@section('title', __('Users Management'))

@section('header', __('Users'))

@section('content')
    <div class="grid grid-cols-1 gap-6">
        {{-- Your content here --}}
    </div>
@endsection
```

**Include admin partials:**
```blade
@include('admin::partials.admin-sidebar')
@include('admin::profile.partials.update-profile-form')
```

### Public Views

Public views use the `public::` namespace and extend the public layout.

**Example:**
```blade
{{-- resources/views/public/pages/about.blade.php --}}
@extends('public::layouts.main')

@section('title', __('About Us'))

@section('content')
    <section class="py-12">
        {{-- Your content here --}}
    </section>
@endsection
```

**Include public partials:**
```blade
@include('public::partials.header')
@include('public::partials.footer')
@include('public::partials.navigation')
```

### Auth Views

Auth views use standard Laravel paths (no namespace) for better compatibility with Laravel Breeze/Jetstream.

**Example:**
```blade
{{-- resources/views/auth/login.blade.php --}}
@extends('auth.layouts.guest')

@section('content')
    {{-- Login form --}}
@endsection
```

### Using Components

Components are shared across the entire application and don't require a namespace:

```blade
<x-input-label for="name" value="{{ __('Name') }}" />
<x-text-input id="name" name="name" type="text" />
<x-input-error :messages="$errors->get('name')" />
<x-primary-button>{{ __('Submit') }}</x-primary-button>
```

## Available Layouts

### 1. Admin Layout (`admin::layouts.admin`)

**Purpose:** Admin dashboard and management pages

**Features:**
- Sidebar navigation (desktop + mobile responsive)
- Top bar with user menu and tenant indicator
- Flash messages support
- Dark mode support

**Sections:**
- `@section('title')` - Page title (appears in browser tab)
- `@section('header')` - Page header (H1 above content)
- `@section('content')` - Main content area

**Example:**
```blade
@extends('admin::layouts.admin')

@section('title', __('Dashboard'))
@section('header', __('Dashboard'))

@section('content')
    {{-- Your admin content --}}
@endsection
```

### 2. Public Main Layout (`public::layouts.main`)

**Purpose:** Public-facing pages (home, about, contact)

**Features:**
- Header with navigation
- Footer
- Clean, marketing-focused design
- Responsive

**Sections:**
- `@section('title')` - Page title
- `@section('content')` - Main content

**Example:**
```blade
@extends('public::layouts.main')

@section('title', __('Home') . ' - ' . config('app.name'))

@section('content')
    {{-- Your public content --}}
@endsection
```

### 3. Guest Layout (`auth.layouts.guest`)

**Purpose:** Authentication pages (login, register, password reset)

**Features:**
- Centered card layout
- Application logo
- Minimal design, focused on forms

**Sections:**
- `@section('content')` - Main content

**Example:**
```blade
@extends('auth.layouts.guest')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        {{-- Login form fields --}}
    </form>
@endsection
```

## Controllers and View Usage

### Admin Controllers

```php
// app/Presentation/Http/Controllers/Admin/DashboardController.php

public function index(): View
{
    return view('admin::dashboard.index', [
        'data' => $data
    ]);
}
```

### Public Controllers

```php
// app/Presentation/Http/Controllers/Web/HomeController.php

public function index(): View
{
    return view('public::pages.home');
}
```

### Auth Controllers

```php
// Standard Laravel auth controllers

public function create(): View
{
    return view('auth.login');
}
```

## Best Practices

### 1. Always Use Correct Namespace

```blade
✅ Admin views
@extends('admin::layouts.admin')
@include('admin::partials.admin-sidebar')

✅ Public views
@extends('public::layouts.main')
@include('public::partials.header')

✅ Auth views (no namespace)
@extends('auth.layouts.guest')

❌ Don't mix namespaces
@extends('theme::layouts.admin')  // Old, deprecated
```

### 2. Components Don't Need Namespace

```blade
✅ Global components (correct)
<x-input-label />
<x-primary-button />

❌ Don't use namespace with components
<x-admin::input-label />
```

### 3. Directory Placement Rules

- **Admin-specific** → `resources/views/admin/`
- **Public pages** → `resources/views/public/pages/`
- **Public partials** → `resources/views/public/partials/`
- **Auth views** → `resources/views/auth/`
- **Global components** → `resources/views/components/`

### 4. Keep Structure Clean

- **Layouts** = Full page structures with HTML skeleton
- **Partials** = Reusable sections (header, footer, sidebar)
- **Components** = Small, reusable UI elements (buttons, inputs)
- **Pages** = Actual content pages that extend layouts

## Architecture Benefits

### Clear Separation of Concerns
- Admin logic is isolated in `admin/`
- Public logic is isolated in `public/`
- No confusion about what belongs where

### Namespace Isolation
- No name collisions between admin and public views
- Clear, explicit view references: `admin::` vs `public::`

### Scalability
- Easy to add new sections (e.g., `api::` for API documentation views)
- Easy to add tenant-specific views in the future

### Maintainability
- Changes to admin don't affect public and vice versa
- Easier to find and organize views

## View Configuration

The view namespaces are registered in:

```php
// app/Infrastructure/Providers/ViewServiceProvider.php

public function boot(): void
{
    // Register theme namespace (legacy support)
    View::addNamespace('theme', resource_path('views/themes/default'));

    // Register admin namespace
    View::addNamespace('admin', resource_path('views/admin'));

    // Register public namespace
    View::addNamespace('public', resource_path('views/public'));
}
```

This provider is registered in `bootstrap/providers.php`.

## Troubleshooting

### View Not Found Error

```
View [admin::dashboard.index] not found.
```

**Solutions:**
1. Clear view cache: `php artisan view:clear`
2. Clear config cache: `php artisan config:clear`
3. Clear bootstrap cache: `rm -rf bootstrap/cache/*.php`
4. Verify file exists at: `resources/views/admin/dashboard/index.blade.php`
5. Check `ViewServiceProvider` is registered in `bootstrap/providers.php`

### Namespace Not Registered

```bash
# Check registered namespaces in tinker
php artisan tinker
>>> app('view')->getFinder()->getHints();
```

You should see:
```php
[
    "admin" => ["/path/to/resources/views/admin"],
    "public" => ["/path/to/resources/views/public"],
    "theme" => ["/path/to/resources/views/themes/default"]
]
```

### Check if View Exists

```bash
php artisan tinker
>>> view()->exists('admin::dashboard.index')  // Should return true
>>> view()->exists('public::pages.home')      // Should return true
>>> view()->exists('auth.login')              // Should return true
```

## Useful Commands

```bash
# Clear all view caches
php artisan view:clear

# Clear config cache
php artisan config:clear

# Clear application cache
php artisan cache:clear

# Clear all caches (including bootstrap)
php artisan optimize:clear

# List all routes and their views
php artisan route:list

# Boot application and check for errors
php artisan about
```

## Multi-Tenancy Support

All views respect tenant context:
- User's current tenant: `auth()->user()->currentTenant()`
- Admin layouts display current tenant name in top bar
- All queries are automatically scoped to current tenant
- Tenant switching updates view context automatically

## Internationalization

All user-facing text should be wrapped in translation helpers:

```blade
{{-- Admin translations --}}
{{ __('admin.dashboard.title') }}
{{ __('admin.submissions.total') }}

{{-- Public translations --}}
{{ __('app.hero.welcome') }}
{{ __('app.footer.copyright') }}

{{-- Auth translations --}}
{{ __('auth.login.title') }}
```

Translation files are located in `lang/` directory.

## Dark Mode Support

All layouts support dark mode using Tailwind CSS:

```blade
{{-- Dark mode classes --}}
<div class="bg-white dark:bg-gray-800">
    <p class="text-gray-900 dark:text-white">
        Content that adapts to dark mode
    </p>
</div>
```

Dark mode toggle is available in:
- Admin layout: Top bar
- Public layout: Navigation
- Preference is saved to browser localStorage

## Migration from Old Structure

If you're updating old views, here's the migration guide:

### Old Theme System (Deprecated)
```blade
@extends('theme::layouts.admin')      → @extends('admin::layouts.admin')
@extends('theme::layouts.marketing')  → @extends('public::layouts.main')
@extends('theme::layouts.guest')      → @extends('auth.layouts.guest')
@include('theme::partials.header')    → @include('public::partials.header')
```

### Controller Updates
```php
// Old
return view('admin.dashboard');

// New
return view('admin::dashboard.index');
```

---

**Last Updated:** 2026-01-12
**View Architecture Version:** 2.0 (Refactored)
**Laravel Version:** 11.x
**PHP Version:** 8.1+
