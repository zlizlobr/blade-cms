# Blade CMS Views Documentation

## Directory Structure

```
resources/views/
├── admin/                      # Admin-specific views
│   ├── dashboard.blade.php
│   └── submissions/
├── auth/                       # Authentication views
├── components/                 # Shared Blade components (available app-wide)
│   ├── application-logo.blade.php
│   ├── input-error.blade.php
│   └── ...
├── profile/                    # User profile views
├── themes/                     # Theme system
│   └── default/               # Default theme
│       ├── layouts/           # Theme layouts
│       │   ├── admin.blade.php
│       │   ├── marketing.blade.php
│       │   ├── guest.blade.php
│       │   └── app.blade.php
│       └── partials/          # Theme partials
│           ├── header.blade.php
│           ├── footer.blade.php
│           └── admin-sidebar.blade.php
└── dashboard.blade.php
```

## Theme System

### Using Theme Namespace

The theme system uses Laravel's view namespace feature. All theme-specific layouts and partials are accessed using the `theme::` prefix.

**Registered namespace:**
- `theme::` → `resources/views/themes/default/`

### Extending Layouts

When creating new views, extend theme layouts using the namespace:

```blade
{{-- Admin pages --}}
@extends('theme::layouts.admin')

{{-- Marketing/public pages --}}
@extends('theme::layouts.marketing')

{{-- Guest pages (login, register) --}}
@extends('theme::layouts.guest')

{{-- Authenticated user pages --}}
@extends('theme::layouts.app')
```

### Including Partials

Include theme partials using the namespace:

```blade
@include('theme::partials.header')
@include('theme::partials.footer')
@include('theme::partials.admin-sidebar')
```

### Using Components

Components are shared across the entire application and don't require a namespace:

```blade
<x-input-label for="name" value="{{ __('Name') }}" />
<x-text-input id="name" name="name" type="text" />
<x-input-error :messages="$errors->get('name')" />
<x-primary-button>{{ __('Submit') }}</x-primary-button>
```

## Creating New Views

### Admin View Example

```blade
{{-- resources/views/admin/users/index.blade.php --}}
@extends('theme::layouts.admin')

@section('title', __('Users Management'))

@section('header', __('Users'))

@section('content')
    <div class="grid grid-cols-1 gap-6">
        {{-- Your content here --}}
    </div>
@endsection
```

### Public/Marketing View Example

```blade
{{-- resources/views/about.blade.php --}}
@extends('theme::layouts.marketing')

@section('title', __('About Us'))

@section('content')
    <section class="py-12">
        {{-- Your content here --}}
    </section>
@endsection
```

## Available Layouts

### 1. Admin Layout (`theme::layouts.admin`)
**Purpose:** Admin dashboard and management pages
**Features:**
- Sidebar navigation
- Top bar with user menu
- Tenant indicator
- Mobile responsive sidebar
- Flash messages support

**Sections:**
- `@section('title')` - Page title
- `@section('header')` - Page header (optional)
- `@section('content')` - Main content

### 2. Marketing Layout (`theme::layouts.marketing`)
**Purpose:** Public-facing pages (home, landing pages)
**Features:**
- Header with navigation
- Footer
- Clean, minimal structure

**Sections:**
- `@section('title')` - Page title
- `@section('content')` - Main content

### 3. Guest Layout (`theme::layouts.guest`)
**Purpose:** Authentication pages (login, register, password reset)
**Features:**
- Centered card layout
- Logo/branding
- Minimal navigation

**Sections:**
- `@section('content')` - Main content

### 4. App Layout (`theme::layouts.app`)
**Purpose:** Authenticated user pages (dashboard, profile)
**Features:**
- Top navigation
- User menu
- Responsive design

**Sections:**
- `@section('header')` - Page header
- `@section('content')` - Main content

## Theme Configuration

The theme namespace is registered in:
```
app/Infrastructure/Providers/ThemeViewServiceProvider.php
```

```php
public function boot(): void
{
    View::addNamespace('theme', resource_path('views/themes/default'));
}
```

## Best Practices

### 1. Always Use Namespace for Theme Files
```blade
✅ @extends('theme::layouts.admin')
❌ @extends('layouts.admin')

✅ @include('theme::partials.header')
❌ @include('partials.header')
```

### 2. Components Don't Need Namespace
```blade
✅ <x-input-label />
❌ <x-theme::input-label />
```

### 3. Directory Placement
- Admin-specific views → `resources/views/admin/`
- Public views → `resources/views/` (root)
- Theme layouts → `resources/views/themes/default/layouts/`
- Theme partials → `resources/views/themes/default/partials/`
- Shared components → `resources/views/components/`

### 4. Keep Theme Structure Clean
- **Layouts** = Full page structures with HTML skeleton
- **Partials** = Reusable sections (header, footer, sidebar)
- **Components** = Small, reusable UI elements

## Switching Themes (Future)

To support multiple themes in the future:

1. Create new theme directory:
   ```
   resources/views/themes/custom-theme/
   ```

2. Update `ThemeViewServiceProvider`:
   ```php
   $themeName = config('app.theme', 'default');
   View::addNamespace('theme', resource_path("views/themes/{$themeName}"));
   ```

3. Add to `.env`:
   ```env
   THEME=custom-theme
   ```

## Troubleshooting

### View Not Found Error
```
View [theme::layouts.admin] not found.
```

**Solutions:**
1. Clear view cache: `php artisan view:clear`
2. Clear config cache: `php artisan config:clear`
3. Verify file exists at: `resources/views/themes/default/layouts/admin.blade.php`
4. Check `ThemeViewServiceProvider` is registered in `bootstrap/providers.php`

### Namespace Not Registered
```php
// Check registered namespaces in tinker
php artisan tinker
>>> app('view')->getFinder()->getHints();
```

You should see:
```php
"theme" => [
    "/path/to/resources/views/themes/default"
]
```

## Commands

```bash
# Clear all view caches
php artisan view:clear

# Clear config cache
php artisan config:clear

# Optimize (compile all views)
php artisan optimize

# Check if view exists
php artisan tinker
>>> view()->exists('theme::layouts.admin')
```

## Multi-Tenancy Support

All views respect tenant context:
- User's current tenant is available via `auth()->user()->currentTenant()`
- Admin layouts display current tenant name
- All queries are automatically scoped to current tenant

## Internationalization

All text should be wrapped in translation helpers:

```blade
{{ __('admin.dashboard.title') }}
{{ __('app.hero.welcome') }}
```

Translation files are located in `lang/` directory.

---

**Last Updated:** 2026-01-12
**Theme System Version:** 1.0
**Laravel Version:** 11.x
