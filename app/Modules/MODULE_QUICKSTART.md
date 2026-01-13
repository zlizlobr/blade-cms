# Module System - Quick Start Guide

## 5-Minute Module Creation

This guide will help you create your first module in 5 minutes.

---

## Step 1: Create Module Structure (1 min)

```bash
# Replace 'MyModule' with your module name
MODULE_NAME="MyModule"

mkdir -p app/Modules/${MODULE_NAME}/{Config,Providers,Routes,Views}
```

---

## Step 2: Create module.json (1 min)

Create `app/Modules/MyModule/module.json`:

```json
{
  "name": "My Module",
  "slug": "my-module",
  "version": "1.0.0",
  "description": "My first module",
  "core_compatibility": "^1.0",
  "dependencies": {}
}
```

---

## Step 3: Create Service Provider (1 min)

Create `app/Modules/MyModule/Providers/ModuleServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\MyModule\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Views', 'my-module');
    }
}
```

---

## Step 4: Create Routes (1 min)

Create `app/Modules/MyModule/Routes/web.php`:

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/my-module', function () {
    return view('my-module::index');
})->name('my-module.index');
```

---

## Step 5: Create View (1 min)

Create `app/Modules/MyModule/Views/index.blade.php`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>My Module</title>
</head>
<body>
    <h1>Hello from My Module!</h1>
    <p>This is my first module.</p>
</body>
</html>
```

---

## Step 6: Install & Activate

### Via Tinker

```bash
php artisan tinker
```

```php
$service = app(\App\Domain\Module\Services\ModuleServiceInterface::class);

// Install
$module = $service->install('my-module', [
    'name' => 'My Module',
    'slug' => 'my-module',
    'version' => '1.0.0',
    'description' => 'My first module',
]);

// Activate
$service->activate('my-module');
```

### Via Admin UI

1. Go to `/admin/modules`
2. Find your module
3. Click "Activate"

---

## Step 7: Test It!

Visit: `http://your-app.test/my-module`

You should see: "Hello from My Module!"

---

## What's Next?

- Add controllers: `app/Modules/MyModule/Controllers/`
- Add models: `app/Modules/MyModule/Models/`
- Add migrations: `app/Modules/MyModule/Migrations/`
- Add config: `app/Modules/MyModule/Config/`

See full documentation: [MODULES.md](MODULES.md)

---

## Common Commands

```bash
# View all modules
php artisan tinker --execute="app(\App\Domain\Module\Services\ModuleServiceInterface::class)->getActiveModules()"

# Activate module
php artisan tinker --execute="app(\App\Domain\Module\Services\ModuleServiceInterface::class)->activate('my-module')"

# Deactivate module
php artisan tinker --execute="app(\App\Domain\Module\Services\ModuleServiceInterface::class)->deactivate('my-module')"
```

---

## Troubleshooting

**Module doesn't appear**:
- Check `module.json` exists and is valid JSON
- Verify namespace in `ModuleServiceProvider`
- Run `composer dump-autoload`

**Routes don't work**:
- Check routes are loaded in `boot()` method
- Verify route file path is correct
- Check module is ACTIVE

**Views not found**:
- Verify view namespace matches `loadViewsFrom()` second parameter
- Use `my-module::viewname` syntax in views
- Check view files have `.blade.php` extension
