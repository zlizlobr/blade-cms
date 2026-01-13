# Module System Documentation

## Overview

Blade CMS includes a powerful modular system that allows you to extend functionality through self-contained modules. Modules can be activated, deactivated, and managed through the admin interface.

## Features

- **Hot-reload**: Modules activate immediately without server restart
- **Dependency Management**: Modules can depend on other modules with version constraints
- **Multi-tenancy**: Support for global and tenant-specific modules
- **Version Control**: Semantic versioning with Composer-style constraints (^, ~, >=)
- **Lifecycle Events**: Hooks for install, activate, deactivate, and uninstall operations

---

## Module Structure

A module should follow this directory structure:

```
app/Modules/{ModuleName}/
├── Config/
│   └── {module}.php          # Module configuration
├── Controllers/
│   └── *Controller.php       # HTTP controllers
├── Models/
│   └── *.php                 # Eloquent models
├── Migrations/
│   └── *.php                 # Database migrations
├── Providers/
│   └── ModuleServiceProvider.php  # Main service provider
├── Routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes (optional)
├── Views/
│   └── *.blade.php           # Blade templates
└── module.json               # Module metadata (required)
```

---

## Creating a Module

### Step 1: Create Module Directory

```bash
mkdir -p app/Modules/YourModule/{Config,Controllers,Models,Migrations,Providers,Routes,Views}
```

### Step 2: Create `module.json`

The `module.json` file contains metadata about your module:

```json
{
  "name": "Your Module Name",
  "slug": "your-module",
  "version": "1.0.0",
  "description": "A brief description of your module",
  "core_compatibility": "^1.0",
  "dependencies": {
    "another-module": "^2.0"
  },
  "author": "Your Name",
  "license": "MIT",
  "providers": [
    "App\\Modules\\YourModule\\Providers\\ModuleServiceProvider"
  ]
}
```

#### Field Descriptions

- **name** (required): Human-readable module name
- **slug** (required): Unique identifier (lowercase, hyphens allowed)
- **version** (required): Semantic version (e.g., "1.0.0")
- **description** (optional): Brief description of module functionality
- **core_compatibility** (optional): CMS version constraint (e.g., "^1.0")
- **dependencies** (optional): Object of module dependencies with version constraints
- **author** (optional): Module author name
- **license** (optional): License type (e.g., "MIT", "GPL-3.0")
- **providers** (optional): Array of service provider class names

### Step 3: Create ModuleServiceProvider

Create `Providers/ModuleServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\YourModule\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register module config
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/your-module.php',
            'your-module'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Views', 'your-module');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        // Publish assets (optional)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/your-module.php' => config_path('your-module.php'),
            ], 'your-module-config');
        }
    }
}
```

### Step 4: Create Routes

Create `Routes/web.php`:

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('your-module')->name('your-module.')->group(function () {
    Route::get('/', function () {
        return view('your-module::index');
    })->name('index');
});
```

### Step 5: Create Views

Create `Views/index.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Your Module</title>
</head>
<body>
    <h1>Your Module</h1>
    <p>Welcome to your module!</p>
</body>
</html>
```

---

## Installing a Module

### Via Code

```php
use App\Domain\Module\Services\ModuleServiceInterface;

$moduleService = app(ModuleServiceInterface::class);

// Parse module.json
$moduleJson = json_decode(
    file_get_contents(app_path('Modules/YourModule/module.json')),
    true
);

// Install module
$module = $moduleService->install($moduleJson['slug'], [
    'name' => $moduleJson['name'],
    'slug' => $moduleJson['slug'],
    'version' => $moduleJson['version'],
    'description' => $moduleJson['description'] ?? null,
    'core_compatibility' => $moduleJson['core_compatibility'] ?? null,
    'dependencies' => $moduleJson['dependencies'] ?? null,
]);
```

### Via Tinker

```bash
php artisan tinker
```

```php
$service = app(\App\Domain\Module\Services\ModuleServiceInterface::class);

$module = $service->install('your-module', [
    'name' => 'Your Module',
    'slug' => 'your-module',
    'version' => '1.0.0',
    'description' => 'Module description',
    'core_compatibility' => '^1.0',
]);
```

---

## Module Lifecycle

### States

A module can be in one of three states:

1. **INSTALLED**: Module is registered but not active
2. **ACTIVE**: Module is running and functional
3. **INACTIVE**: Module was active but has been deactivated

### Activating a Module

```php
$moduleService->activate('your-module');
```

**Requirements**:
- Module must be in INSTALLED or INACTIVE state
- All dependencies must be ACTIVE
- Version constraints must be satisfied
- No circular dependencies

**What happens**:
1. Status changes to ACTIVE
2. `enabled_at` timestamp is set
3. `ModuleActivated` event is dispatched
4. Service provider is registered immediately (hot-reload)

### Deactivating a Module

```php
$moduleService->deactivate('your-module');
```

**Requirements**:
- Module must be ACTIVE
- No other ACTIVE modules depend on it

**What happens**:
1. Status changes to INACTIVE
2. `ModuleDeactivated` event is dispatched
3. Cache flag is set for next request
4. Full deactivation on next request

### Checking Module Status

```php
// Check if module can be activated
$canActivate = $moduleService->canActivate('your-module');

// Check if module can be deactivated
$canDeactivate = $moduleService->canDeactivate('your-module');

// Get module dependencies
$dependencies = $moduleService->getModuleDependencies('your-module');
```

---

## Dependencies

### Declaring Dependencies

In `module.json`:

```json
{
  "dependencies": {
    "blog": "^1.0",
    "pages": "~2.3",
    "media": ">=1.5.0"
  }
}
```

### Version Constraints

Supports Composer-style semantic versioning:

- **Exact**: `"1.0.0"` - Exactly version 1.0.0
- **Caret**: `"^1.0"` - Compatible with 1.x (>=1.0.0, <2.0.0)
- **Tilde**: `"~1.2"` - Approximate version (~1.2 = >=1.2.0, <1.3.0)
- **Greater than**: `">=1.5"` - Version 1.5 or higher
- **Range**: `">=1.0 <2.0"` - Between 1.0 and 2.0

### Dependency Resolution

The system automatically:
- Resolves dependency order (topological sort)
- Detects circular dependencies
- Validates version constraints
- Ensures all dependencies are ACTIVE before activation

---

## Multi-Tenancy

Modules can be:

- **Global**: Available to all tenants (`tenant_id = NULL`)
- **Tenant-specific**: Available only to specific tenant

When querying modules, the system automatically includes:
- Global modules (tenant_id = NULL)
- Current tenant's modules

---

## Events

The module system dispatches events for lifecycle operations:

### ModuleInstalled

Dispatched when a module is installed.

```php
use App\Domain\Module\Events\ModuleInstalled;

Event::listen(ModuleInstalled::class, function ($event) {
    $module = $event->module;
    // Handle installation
});
```

### ModuleActivated

Dispatched when a module is activated.

```php
use App\Domain\Module\Events\ModuleActivated;

Event::listen(ModuleActivated::class, function ($event) {
    $module = $event->module;
    // Handle activation
});
```

### ModuleDeactivated

Dispatched when a module is deactivated.

```php
use App\Domain\Module\Events\ModuleDeactivated;

Event::listen(ModuleDeactivated::class, function ($event) {
    $module = $event->module;
    // Handle deactivation
});
```

### ModuleUninstalled

Dispatched when a module is uninstalled.

```php
use App\Domain\Module\Events\ModuleUninstalled;

Event::listen(ModuleUninstalled::class, function ($event) {
    $module = $event->module;
    // Handle uninstallation
});
```

---

## Admin Interface

Modules can be managed through the admin panel at `/admin/modules`:

- **View all modules**: Grid view with status badges
- **Filter by status**: Active, Inactive, Installed
- **View details**: Dependencies, version, compatibility
- **Activate/Deactivate**: One-click toggle with validation
- **View dependencies**: Visual dependency tree

---

## Example: Blog Module

See the complete example in `app/Modules/Blog/`:

```
app/Modules/Blog/
├── Config/blog.php
├── Providers/ModuleServiceProvider.php
├── Routes/web.php
├── Views/
│   ├── index.blade.php
│   └── post.blade.php
└── module.json
```

To install and activate:

```php
use App\Domain\Module\Services\ModuleServiceInterface;

$service = app(ModuleServiceInterface::class);

// Install
$module = $service->install('blog', [
    'name' => 'Blog Module',
    'slug' => 'blog',
    'version' => '1.0.0',
    'description' => 'A simple blog module',
    'core_compatibility' => '^1.0',
]);

// Activate
$service->activate('blog');

// Visit http://your-app.test/blog
```

---

## Best Practices

1. **Use strict typing**: Always include `declare(strict_types=1);`
2. **Follow DDD architecture**: Keep domain logic separate
3. **Version properly**: Follow semantic versioning
4. **Document dependencies**: Clearly specify version constraints
5. **Test thoroughly**: Test install/activate/deactivate cycle
6. **Handle cleanup**: Clean up resources on deactivation
7. **Use events**: Leverage lifecycle events for side effects
8. **Namespace properly**: Use `App\Modules\{ModuleName}` namespace

---

## Troubleshooting

### Module won't activate

- Check dependencies are installed and active
- Verify version constraints are satisfied
- Check for circular dependencies
- Review logs for errors

### Hot-reload not working

- Ensure `ModuleServiceProvider` is registered correctly
- Check `RegisterModuleProviders` listener is working
- Verify no syntax errors in service provider

### Dependencies not found

- Ensure dependency modules are installed
- Check slug spelling in `module.json`
- Verify version constraints are achievable

---

## API Reference

### ModuleServiceInterface

```php
interface ModuleServiceInterface
{
    public function install(string $slug, array $metadata): Module;
    public function activate(string $slug): Module;
    public function deactivate(string $slug): Module;
    public function uninstall(string $slug): bool;
    public function getActiveModules(): Collection;
    public function getModuleBySlug(string $slug): ?Module;
    public function canActivate(string $slug): bool;
    public function canDeactivate(string $slug): bool;
    public function getModuleDependencies(string $slug): array;
}
```

---

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Review documentation: `docs/MODULES.md`
- Contact support: support@blade-cms.com
