# View Architecture Refactoring Proposal
## Oddělení Public a Admin Logiky v Blade-CMS

**Datum:** 2026-01-12
**Status:** Návrh k diskuzi
**Cíl:** Jasně oddělit public a admin view logiku pro lepší maintainability a scalabilitu

---

## 📊 Současný Stav (As-Is)

### Struktura
```
resources/views/
├── admin/                   # Admin views (8 souborů)
│   ├── dashboard.blade.php
│   ├── layouts/admin.blade.php
│   ├── partials/admin-sidebar.blade.php
│   ├── profile/             # 4 soubory
│   └── submissions/         # 2 soubory
├── auth/                    # Auth views (6 souborů)
├── components/              # Shared komponenty (14 souborů)
├── themes/default/          # Theme systém
│   ├── layouts/             # 5 layoutů (mix public + admin)
│   └── partials/            # 4 partial (mix public + admin)
└── home.blade.php          # Public homepage
```

### Problémy Současného Řešení

1. **Nekonzistentní namespace usage**
   - `admin::` namespace se používá, ale není registrován v providers
   - Profile partials používají `theme::` ale jsou ve složce `admin/`

2. **Smíšená logika v theme složce**
   - `themes/default/layouts/admin.blade.php` - admin layout v theme složce
   - `themes/default/partials/admin-sidebar.blade.php` - admin partial v theme složce
   - Theme systém by měl obsahovat jen public/marketing layouty

3. **Duplicitní admin layouty**
   - `admin/layouts/admin.blade.php` (prakticky nevyužitý)
   - `themes/default/layouts/admin.blade.php` (aktuálně používaný)

4. **Nepřehledné rozdělení odpovědností**
   - Není jasné, co patří do theme systému a co do admin/public oblastí
   - Components jsou sdílené, ale chybí admin-specific komponenty

---

## 🎯 Navrhovaná Struktura (To-Be)

### Varianta A: Oddělené Top-Level Složky (DOPORUČENO)

```
resources/views/
├── admin/                          # 🔒 Admin Area (kompletní izolace)
│   ├── layouts/
│   │   ├── admin.blade.php        # Hlavní admin layout se sidebar
│   │   └── admin-minimal.blade.php # Bez sidebar (modály, tisk)
│   ├── partials/
│   │   ├── sidebar.blade.php      # Admin sidebar navigace
│   │   ├── topbar.blade.php       # Top navigation bar
│   │   ├── tenant-indicator.blade.php
│   │   └── breadcrumbs.blade.php
│   ├── components/                 # Admin-specific komponenty
│   │   ├── stats-card.blade.php
│   │   ├── data-table.blade.php
│   │   ├── action-button.blade.php
│   │   └── status-badge.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── profile/
│   │   ├── edit.blade.php
│   │   └── partials/
│   │       ├── update-profile-form.blade.php
│   │       ├── update-password-form.blade.php
│   │       └── delete-user-form.blade.php
│   ├── submissions/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── users/                      # Nové: User management
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── settings/                   # Nové: Admin settings
│       └── index.blade.php
│
├── public/                         # 🌍 Public Website
│   ├── layouts/
│   │   ├── main.blade.php         # Hlavní public layout
│   │   ├── minimal.blade.php      # Minimální (bez header/footer)
│   │   └── landing.blade.php      # Landing pages s hero sekcí
│   ├── partials/
│   │   ├── header.blade.php       # Public header
│   │   ├── footer.blade.php       # Public footer
│   │   ├── navigation.blade.php   # Public navigace
│   │   └── hero.blade.php         # Hero sekce
│   ├── components/                 # Public-specific komponenty
│   │   ├── feature-card.blade.php
│   │   ├── testimonial.blade.php
│   │   ├── contact-form.blade.php
│   │   └── newsletter-form.blade.php
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── about.blade.php
│   │   ├── contact.blade.php
│   │   └── privacy.blade.php
│   └── blog/                       # Příklad: Blog sekce
│       ├── index.blade.php
│       └── show.blade.php
│
├── auth/                           # 🔐 Authentication (sdílené)
│   ├── layouts/
│   │   └── guest.blade.php        # Auth layout (login/register)
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   ├── verify-email.blade.php
│   └── confirm-password.blade.php
│
├── components/                     # 🔧 Global Shared Components
│   ├── ui/                         # UI primitives (použitelné všude)
│   │   ├── button.blade.php
│   │   ├── input.blade.php
│   │   ├── label.blade.php
│   │   ├── modal.blade.php
│   │   ├── dropdown.blade.php
│   │   └── alert.blade.php
│   ├── forms/
│   │   ├── text-input.blade.php
│   │   ├── textarea.blade.php
│   │   ├── select.blade.php
│   │   ├── checkbox.blade.php
│   │   └── input-error.blade.php
│   └── utils/
│       ├── application-logo.blade.php
│       ├── language-switcher.blade.php
│       └── dark-mode-toggle.blade.php
│
└── errors/                         # 🚨 Error pages
    ├── 404.blade.php
    ├── 403.blade.php
    ├── 500.blade.php
    └── 503.blade.php
```

### Výhody Varianty A
✅ **Jasná separace**: Admin a public jsou top-level složky
✅ **Snadná navigace**: Okamžitě vidíte, kde co je
✅ **Namespace podpora**: Jednoduché registrovat `admin::` a `public::` namespace
✅ **Scalabilita**: Snadné přidávat nové sekce (např. `api/` pro API views)
✅ **Tenant isolation**: V budoucnu lze přidat `tenant/` složku pro tenant-specific views

---

### Varianta B: Theme-Based Struktura (Alternativa)

```
resources/views/
├── themes/
│   ├── admin/                      # Admin theme
│   │   ├── layouts/
│   │   ├── partials/
│   │   ├── components/
│   │   └── pages/
│   └── public/                     # Public theme
│       ├── layouts/
│       ├── partials/
│       ├── components/
│       └── pages/
├── auth/                           # Shared auth
├── components/                     # Global shared
└── errors/
```

### Nevýhody Varianty B
❌ Složitější namespace management
❌ Admin není "theme", je to samostatná aplikační oblast
❌ Méně intuitivní pro vývojáře

---

## 🔧 Namespace Registrace (Pro Variantu A)

### V `ThemeViewServiceProvider.php`

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ThemeViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Admin namespace
        View::addNamespace('admin', resource_path('views/admin'));

        // Public namespace
        View::addNamespace('public', resource_path('views/public'));

        // Auth namespace (optional, lze použít default)
        View::addNamespace('auth', resource_path('views/auth'));
    }
}
```

### Použití v Blade

```blade
{{-- Admin views --}}
@extends('admin::layouts.admin')
@include('admin::partials.sidebar')
<x-admin::stats-card />

{{-- Public views --}}
@extends('public::layouts.main')
@include('public::partials.header')
<x-public::feature-card />

{{-- Auth views --}}
@extends('auth::layouts.guest')

{{-- Global components (bez namespace) --}}
<x-ui.button />
<x-forms.text-input />
<x-utils.language-switcher />
```

---

## 📝 Migrace Tasks

### Phase 1: Příprava a Plánování ✅
- [x] Analyzovat současnou strukturu views
- [x] Navrhnout novou strukturu
- [x] Získat schválení architektury

### Phase 2: Vytvoření Nové Struktury 📁

#### Task 2.1: Vytvořit Admin Složku
```bash
# Vytvořit novou admin strukturu
mkdir -p resources/views/admin/{layouts,partials,components,dashboard,profile/partials,submissions,users,settings}
```

**Soubory k vytvoření:**
- `admin/layouts/admin.blade.php` - Hlavní admin layout
- `admin/layouts/admin-minimal.blade.php` - Minimální layout
- `admin/partials/sidebar.blade.php` - Přesunout z themes
- `admin/partials/topbar.blade.php` - Nový top bar
- `admin/partials/tenant-indicator.blade.php` - Tenant info
- `admin/partials/breadcrumbs.blade.php` - Breadcrumb navigace

#### Task 2.2: Vytvořit Public Složku
```bash
# Vytvořit public strukturu
mkdir -p resources/views/public/{layouts,partials,components,pages,blog}
```

**Soubory k vytvoření:**
- `public/layouts/main.blade.php` - Hlavní public layout
- `public/layouts/landing.blade.php` - Landing pages
- `public/partials/header.blade.php` - Přesunout z themes
- `public/partials/footer.blade.php` - Přesunout z themes
- `public/partials/navigation.blade.php` - Přesunout z themes
- `public/pages/home.blade.php` - Přesunout z root

#### Task 2.3: Reorganizovat Components
```bash
# Reorganizovat komponenty
mkdir -p resources/views/components/{ui,forms,utils}
```

**Soubory k přesunutí:**
- `components/primary-button.blade.php` → `components/ui/button.blade.php`
- `components/text-input.blade.php` → `components/forms/text-input.blade.php`
- `components/input-label.blade.php` → `components/forms/label.blade.php`
- `components/input-error.blade.php` → `components/forms/input-error.blade.php`
- `components/modal.blade.php` → `components/ui/modal.blade.php`
- `components/dropdown.blade.php` → `components/ui/dropdown.blade.php`

#### Task 2.4: Vytvořit Auth Složku
```bash
# Auth má vlastní layout
mkdir -p resources/views/auth/layouts
```

**Soubory:**
- Přesunout `themes/default/layouts/guest.blade.php` → `auth/layouts/guest.blade.php`
- Auth views zůstávají v `resources/views/auth/`

### Phase 3: Registrace Namespaces 🔌

#### Task 3.1: Aktualizovat ThemeViewServiceProvider
```php
// app/Infrastructure/Providers/ThemeViewServiceProvider.php

public function boot(): void
{
    View::addNamespace('admin', resource_path('views/admin'));
    View::addNamespace('public', resource_path('views/public'));
    View::addNamespace('auth', resource_path('views/auth'));
}
```

#### Task 3.2: Vytvořit ViewServiceProvider Alias
Zvážit přejmenování `ThemeViewServiceProvider` → `ViewServiceProvider` pro lepší sémantiku.

### Phase 4: Migrace Existujících Views 🔄

#### Task 4.1: Migrovat Admin Views
**Přesunout:**
- `admin/dashboard.blade.php` → `admin/dashboard/index.blade.php`
- `admin/profile/*` → `admin/profile/*` (aktualizovat cesty)
- `admin/submissions/*` → `admin/submissions/*` (aktualizovat cesty)

**Aktualizovat @extends:**
```blade
// Starý způsob
@extends('admin::layouts.admin')

// Nový způsob (pokud namespace již funguje)
@extends('admin::layouts.admin')
```

#### Task 4.2: Migrovat Public Views
**Přesunout:**
- `home.blade.php` → `public/pages/home.blade.php`
- `themes/default/partials/header.blade.php` → `public/partials/header.blade.php`
- `themes/default/partials/footer.blade.php` → `public/partials/footer.blade.php`
- `themes/default/partials/navigation.blade.php` → `public/partials/navigation.blade.php`

**Aktualizovat @extends:**
```blade
// Starý způsob
@extends('theme::layouts.marketing')

// Nový způsob
@extends('public::layouts.main')
```

#### Task 4.3: Migrovat Auth Views
**Aktualizovat @extends:**
```blade
// Starý způsob
@extends('theme::layouts.guest')

// Nový způsob
@extends('auth::layouts.guest')
```

#### Task 4.4: Aktualizovat Profile Partials
**Problém:** Profile views používají `theme::profile.partials.*` ale soubory jsou v `admin/profile/partials/`

**Řešení:**
```blade
// admin/profile/edit.blade.php

// Starý způsob (nefunkční)
@include('theme::profile.partials.update-profile-information-form')

// Nový způsob
@include('admin::profile.partials.update-profile-form')
```

### Phase 5: Aktualizace Controllers 🎮

#### Task 5.1: Admin Controllers
Aktualizovat return views v admin controllers:

```php
// app/Presentation/Http/Controllers/Admin/DashboardController.php

// Starý způsob
return view('admin.dashboard');

// Nový způsob (s namespace)
return view('admin::dashboard.index');
```

**Soubory k aktualizaci:**
- `DashboardController.php`
- `SubmissionController.php`
- `ProfileController.php` (pokud je v admin)

#### Task 5.2: Public Controllers
```php
// app/Presentation/Http/Controllers/HomeController.php

// Starý způsob
return view('home');

// Nový způsob
return view('public::pages.home');
```

#### Task 5.3: Auth Controllers
```php
// Laravel auth controllers

// Starý způsob
return view('auth.login');

// Nový způsob (pokud použijeme namespace)
return view('auth::login');

// NEBO bez namespace (preferováno pro auth)
return view('auth.login');
```

### Phase 6: Vyčištění Starých Souborů 🧹

#### Task 6.1: Smazat Duplicitní Layouty
Po úspěšné migraci smazat:
- `themes/default/layouts/admin.blade.php` (duplikát)
- `themes/default/partials/admin-sidebar.blade.php` (přesunuto do admin/)

#### Task 6.2: Vyčistit Theme Složku
Rozhodnout, zda:
- **Zachovat** `themes/` pro budoucí multi-theme support
- **Smazat** pokud theme systém nebude využit

**Doporučení:** Zachovat `themes/` pro budoucí rozšíření (např. různé public themes).

#### Task 6.3: Aktualizovat .gitignore
Pokud jsou nějaké view cache soubory:
```bash
# .gitignore
/storage/framework/views/
```

### Phase 7: Testování 🧪

#### Task 7.1: Manuální Testování
- [ ] Admin dashboard se zobrazuje správně
- [ ] Admin sidebar navigace funguje
- [ ] Public homepage se zobrazuje
- [ ] Auth views (login, register) fungují
- [ ] Profile edit views se načítají
- [ ] Submissions list a detail views fungují
- [ ] Dark mode funguje v admin i public
- [ ] Language switcher funguje
- [ ] Tenant indicator se zobrazuje v admin

#### Task 7.2: Automatizované Testy
Aktualizovat feature testy:

```php
// tests/Feature/Admin/DashboardTest.php

public function test_dashboard_displays_correctly(): void
{
    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertViewIs('admin::dashboard.index'); // Aktualizovat
}
```

#### Task 7.3: Kontrola Breaků
```bash
# Spustit všechny testy
php artisan test

# Vyčistit cache
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Zkontrolovat, že aplikace bootuje
php artisan serve
```

### Phase 8: Dokumentace 📚

#### Task 8.1: Aktualizovat CLAUDE.md
Přidat sekci o view architektuře:

```markdown
## View Architecture

### Structure
- `admin::` - Admin area views (dashboard, settings, users)
- `public::` - Public website views (homepage, blog, pages)
- `auth::` - Authentication views (login, register)
- Global components - Shared UI components (no namespace)

### Usage
- Admin: `@extends('admin::layouts.admin')`
- Public: `@extends('public::layouts.main')`
- Components: `<x-ui.button />`, `<x-forms.text-input />`
```

#### Task 8.2: Vytvořit VIEW_STRUCTURE.md
Dokumentovat finální strukturu pro budoucí vývojáře.

#### Task 8.3: Aktualizovat README.md (pokud existuje)
Zmínit view architekturu v dokumentaci projektu.

---

## 🎨 Admin vs Public: Design Differences

### Admin Area
- **Layout:** Sidebar + top navigation
- **Styling:** Data-dense, tabulky, formuláře, karty se statistikami
- **Komponenty:** Stats cards, data tables, action buttons, filters
- **Audience:** Authenticated admins/managers
- **Purpose:** Data management, CRUD operations

### Public Area
- **Layout:** Header + content + footer (horizontální navigace)
- **Styling:** Marketing-focused, hero sekce, call-to-actions
- **Komponenty:** Feature cards, testimonials, contact forms
- **Audience:** Anonymní návštěvníci, potenciální zákazníci
- **Purpose:** Prezentace, marketing, lead generation

### Auth Area
- **Layout:** Minimální (centered card)
- **Styling:** Čisté, focused, bez distrakcí
- **Komponenty:** Forms, validation errors
- **Audience:** Uživatelé přihlašující se nebo registrující
- **Purpose:** Autentizace

---

## 🚀 Benefits Po Refactoringu

### Pro Vývojáře
✅ **Jasná struktura** - Okamžitě vidí, kde najít admin vs. public views
✅ **Namespace izolace** - Žádné kolize názvů mezi admin a public
✅ **Snadná navigace** - Logické seskupení souvisejících views
✅ **Komponenty na správném místě** - Admin komponenty v admin/, public v public/

### Pro Projekt
✅ **Scalabilita** - Snadné přidat nové sekce (např. API views)
✅ **Maintainability** - Změny v admin neovlivní public a naopak
✅ **Multi-tenancy ready** - V budoucnu lze přidat tenant-specific views
✅ **Theme support** - Pokud budete chtít multiple themes pro public

### Pro Performance
✅ **View caching** - Laravel může lépe cachovat namespace views
✅ **Autoloading** - Efektivnější načítání views

---

## ⚠️ Rizika a Mitigace

### Riziko 1: Breaking Changes v Production
**Mitigace:**
- Provést migraci postupně ve feature branchi
- Testovat každou fázi před pokračováním
- Použít feature flags pro postupné nasazení

### Riziko 2: Zapomenuté View References
**Mitigace:**
- Použít IDE search (Find All) pro `@extends`, `@include`, `view()`
- Grep přes celý codebase: `grep -r "view('admin" app/`
- Spustit všechny testy před mergem

### Riziko 3: Cache Issues
**Mitigace:**
- Po každé změně spustit `php artisan view:clear`
- Dokumentovat clearing postupy
- Přidat do deployment scriptu

---

## 📋 Checklist Pro Schválení

Před zahájením implementace potvrdit:

- [ ] **Struktura schválena** - Varianta A vs. B
- [ ] **Namespace convention** - `admin::`, `public::`, nebo bez namespace
- [ ] **Theme složka** - Zachovat nebo smazat?
- [ ] **Komponenty** - Organizace `ui/`, `forms/`, `utils/` OK?
- [ ] **Deployment plán** - Jak nasadit bez downtime?
- [ ] **Rollback strategie** - Jak vrátit změny v případě problémů?

---

## 🔄 Rollback Plán

Pokud refactoring způsobí problémy:

1. **Git revert** - Vrátit všechny commity z feature branch
2. **Clear caches** - `php artisan view:clear && php artisan cache:clear`
3. **Redeploy previous version** - Z main branch
4. **Analyzovat chyby** - Log files, error reports
5. **Opravit a znovu** - Fix issues offline, test, redeploy

---

## 📊 Estimated Effort

| Phase | Tasks | Complexity | Time Estimate |
|-------|-------|-----------|---------------|
| 1. Příprava | 3 | Low | ✅ Hotovo |
| 2. Vytvoření struktury | 4 | Low | 1-2 hodiny |
| 3. Registrace namespaces | 2 | Low | 30 minut |
| 4. Migrace views | 4 | Medium | 2-3 hodiny |
| 5. Aktualizace controllers | 3 | Low | 1 hodina |
| 6. Vyčištění | 3 | Low | 30 minut |
| 7. Testování | 3 | High | 2-3 hodiny |
| 8. Dokumentace | 3 | Low | 1 hodina |
| **TOTAL** | **25** | **Medium** | **8-12 hodin** |

---

## 💬 Otázky k Diskuzi

1. **Preferujete Variantu A (top-level folders) nebo Variantu B (theme-based)?**
   - Doporučuji: **Varianta A**

2. **Chcete zachovat `themes/` složku pro budoucí multi-theme support?**
   - Doporučuji: **Ano** (pro flexibilitu)

3. **Použít namespace i pro auth views (`auth::`) nebo ponechat default (`auth.login`)?**
   - Doporučuji: **Default** (jednodušší integrace s Laravel Breeze/Jetstream)

4. **Vytvořit admin-specific a public-specific komponenty, nebo jen sdílené?**
   - Doporučuji: **Obojí** (sdílené + specific pro každou oblast)

5. **Kdy provést migraci?** (Okamžitě vs. postupně vs. čekat na další milestone)
   - Doporučuji: **Postupně** - Phase by phase testing

---

## 📞 Next Steps

1. **Review tohoto dokumentu** - Projít a odsouhlasit architekturu
2. **Odpovědět na otázky výše** - Finalizovat decision points
3. **Vytvořit feature branch** - `feature/view-architecture-refactoring`
4. **Začít s Phase 2** - Postupně implementovat tasky
5. **Code review po každé phase** - Průběžné review místo velkého merge

---

**Vytvořil:** Claude Sonnet 4.5
**Ke schválení:** Projektový tým
**Status:** 🟡 Čeká na schválení
