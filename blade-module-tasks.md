# Modularizace Blade CMS – Task List

## Cíl
Převést existující moduly na samostatné Git repozitáře spravované přes Composer se sémantickým verzováním.

## Implementační strategie
- **Composer Path Repositories** - moduly jako lokální Composer balíčky
- **Lokace**: `../blade-modules/blade-module-{name}/`
- **Symlinky**: Ano (pro rychlý vývoj)
- **CMS repozitář**: Moduly jsou v `.gitignore` (kromě dokumentace)

---

## Task 1 – Vytvoření samostatného repozitáře modulu
- Vytvořit nový Git repozitář `blade-module-<name>`
- Inicializovat Git (`git init`)
- Přesunout logiku modulu z `app/Modules/<Name>` do repozitáře
- Nastavit výchozí branch `main`

---

## Task 2 – Struktura modulu
- Vytvořit strukturu:
  ```
  blade-module-<name>/
  ├── src/
  ├── routes/
  ├── resources/views/
  ├── database/migrations/
  ├── composer.json
  ├── README.md
  ```

---

## Task 3 – Namespace a PSR-4
- Upravit namespace tříd na `Blade\Modules\<Name>`
- Přesunout PHP třídy do `src/`
- Zajistit PSR-4 kompatibilitu

---

## Task 4 – Service Provider
- Vytvořit `<Name>ServiceProvider`
- Registrovat:
  - routes
  - views
  - migrations
- Přidat provider do `composer.json` (extra.laravel.providers)

---

## Task 5 – Composer konfigurace
- Nastavit `name` ve formátu `bladecms/module-<name>`
- Nastavit `autoload.psr-4`
- Nastavit minimální PHP verzi
- Přidat případné závislosti

---

## Task 6 – Test lokální instalace přes path repository
- Upravit `composer.json` v hlavním CMS:
  - přidat `repositories.type = path`
  - zapnout `symlink`
- Ověřit funkčnost modulu v CMS

---

## Task 7 – Git tag a verzování
- Nastavit Semantic Versioning
- Vytvořit tag `v1.0.0`
- Pushnout tag do repozitáře

---

## Task 8 – Přechod na VCS repository
- Odstranit `path` repository z CMS
- Přidat `vcs` repository s Git URL
- Přidat modul do `require` sekce
- Spustit `composer update`

---

## Task 9 – Cleanup původního CMS
- Odstranit `app/Modules/<Name>`
- Odstranit staré autoload konfigurace
- Ověřit, že CMS funguje pouze s Composer modulem

---

## Task 10 – Dokumentace
- Popsat instalaci modulu v README.md
- Popsat verzování a update proces
- Popsat závislosti a kompatibilitu

---

## Task 11 – CI (volitelné)
- Přidat GitHub Actions workflow
- Spustit testy a static analysis
- Blokovat merge bez úspěšného buildu

---

## Rychlý průvodce migrací modulu

### 1. Automatická migrace (doporučeno)

```bash
# Migrovat existující modul (např. Blog)
./scripts/migrate-module.sh Blog
```

Skript automaticky:
- Vytvoří `../blade-modules/blade-module-blog/`
- Inicializuje Git repozitář
- Překopíruje a přestrukturuje soubory
- Vygeneruje `composer.json`
- Vytvoří initial commit + tag `v1.0.0`

### 2. Připojení k CMS

```bash
# Přidat modul do CMS
cd /path/to/blade-cms
composer require bladecms/module-blog:@dev
```

CMS už má v `composer.json` nakonfigurované path repositories:
```json
"repositories": [
    {
        "type": "path",
        "url": "../blade-modules/*",
        "options": {
            "symlink": true
        }
    }
]
```

### 3. Instalace v CMS

```bash
php artisan tinker
```

```php
$service = app(\App\Domain\Module\Services\ModuleServiceInterface::class);
$service->install('blog', [
    'name' => 'Blog Module',
    'slug' => 'blog',
    'version' => '1.0.0',
]);
$service->activate('blog');
```

### 4. Cleanup

```bash
# Odstranit původní modul z CMS (už je symlinkovaný přes Composer)
rm -rf app/Modules/Blog
```

---

## Stav implementace

### ✅ Hotovo

- [x] Task 2: Struktura modulu (Blog modul)
- [x] Task 3: Namespace a PSR-4
- [x] Task 4: Service Provider
- [x] Task 5: Composer konfigurace (module.json)
- [x] Task 10: Dokumentace (MODULES.md, QUICKSTART.md)
- [x] **Git konfigurace**: `.gitignore` pravidla pro moduly
- [x] **Composer setup**: Path repositories konfigurace
- [x] **Migrační skript**: `scripts/migrate-module.sh`

### 🔄 K dokončení pro Blog modul

- [ ] Task 1: Spustit `./scripts/migrate-module.sh Blog`
- [ ] Task 6: `composer require bladecms/module-blog:@dev`
- [ ] Task 7: Git tag už vytvoří skript (v1.0.0)
- [ ] Task 8: Nastavení remote repo (volitelné pro lokální dev)
- [ ] Task 9: `rm -rf app/Modules/Blog`

---

## Poznámky pro další moduly

Při vytváření nového modulu:

1. **Vytvořit rovnou v `../blade-modules/blade-module-{name}/`**
2. **Struktura:**
   ```
   blade-module-{name}/
   ├── src/
   │   ├── Controllers/
   │   ├── Models/
   │   └── Providers/
   │       └── ModuleServiceProvider.php
   ├── routes/
   ├── resources/views/
   ├── database/migrations/
   ├── config/
   ├── composer.json
   ├── module.json
   └── README.md
   ```

3. **Composer.json template:**
   ```json
   {
       "name": "bladecms/module-{slug}",
       "type": "library",
       "require": {
           "php": "^8.2",
           "laravel/framework": "^11.0"
       },
       "autoload": {
           "psr-4": {
               "App\\Modules\\{Name}\\": "src/"
           }
       },
       "extra": {
           "laravel": {
               "providers": [
                   "App\\Modules\\{Name}\\Providers\\ModuleServiceProvider"
               ]
           }
       }
   }
   ```

4. **Instalace:** `composer require bladecms/module-{slug}:@dev`
