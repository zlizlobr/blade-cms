# Test Suite Documentation

This document describes the comprehensive test suite implemented for Blade CMS, including test organization, CI/CD pipeline, and code quality tools.

## 📁 Test Structure

The test suite is organized into four main categories:

```
tests/
├── Unit/                    # Unit tests for services and isolated logic
├── Feature/                 # Feature tests for HTTP routes and workflows
├── Views/                   # View rendering and component tests
├── Api/                     # API endpoint tests (reserved for future)
└── TestCase.php            # Base test case with common setup
```

### Test Suites Configuration

All test suites are configured in `phpunit.xml`:

- **Unit**: Tests for services, repositories, and business logic
- **Feature**: End-to-end tests for user workflows
- **Views**: Blade template and component rendering tests
- **Api**: API endpoint tests (prepared for future implementation)

## 🧪 Test Coverage

### Unit Tests (13 tests)

Located in `tests/Unit/`:

#### FormSubmissionServiceTest
- ✅ Factory method instantiation
- ✅ Creating submissions with existing users
- ✅ Auto-creating new users from form data
- ✅ Correct data structure validation
- ✅ Multi-tenancy isolation

#### DashboardServiceTest
- ✅ Factory method instantiation
- ✅ Statistics array structure
- ✅ Total submissions counting
- ✅ Weekly submissions filtering
- ✅ Active users counting
- ✅ Recent submissions (limited to 5, ordered by latest)
- ✅ Tenant data isolation

### Feature Tests (71 tests)

Located in `tests/Feature/`:

#### Authentication (18 tests)
- Login/logout flows
- Registration process
- Password reset functionality
- Email verification
- Password confirmation
- Form validation

#### Authorization (18 tests)
- **AdminTest**: Role-based access control
  - Admin dashboard access
  - Submissions list and detail views
  - Multi-tenancy isolation
  - Search and filtering

- **FormSubmissionPolicyTest**: Policy-based authorization
  - Admin vs Subscriber permissions
  - ViewAny, View, Create policies
  - Guest submission capabilities

#### Form Submissions (7 tests)
- Form submission validation
- User creation from submissions
- Event dispatching
- AJAX handling

#### Internationalization (4 tests)
- Language switching
- Locale persistence
- Translation loading

#### User Registration Flow (12 tests)
- Registration validation
- Default role assignment
- Auto-login after registration
- Access control verification

#### Profile Management (5 tests)
- Profile display
- Information updates
- Email verification status
- Account deletion

#### Other Feature Tests (7 tests)
- Various application workflows

### View Tests (16 tests)

Located in `tests/Views/`:

#### AdminViewTest (5 tests)
- Dashboard rendering
- Submissions index and detail pages
- Admin layout structure
- Profile edit page

#### AuthViewTest (5 tests)
- Login page
- Registration page
- Password reset pages
- Guest layout

#### PublicViewTest (3 tests)
- Homepage rendering
- Language switcher component
- Public layout structure

#### ComponentTest (3 tests)
- Primary button component
- Dropdown link component
- Language switcher Alpine.js integration

## 🚀 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Suite
```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test --testsuite=Views
php artisan test --testsuite=Api
```

### Run Parallel Tests (faster)
```bash
php artisan test --parallel
```

### Run Specific Test File
```bash
php artisan test tests/Unit/DashboardServiceTest.php
```

### Run with Coverage (requires Xdebug)
```bash
php artisan test --coverage
```

## 🎯 Code Quality Tools

### Laravel Pint

**Configuration**: `pint.json`

Laravel Pint enforces code style standards:
- Laravel preset
- Strict types declaration (`declare(strict_types=1);`)
- Unused imports removal
- Alphabetically ordered imports
- Superfluous PHPDoc tags cleanup

**Usage**:
```bash
# Check code style
./vendor/bin/pint --test

# Fix code style automatically
./vendor/bin/pint
```
```bash
# Spusť s 2GB paměti (jako v CI)
./vendor/bin/phpstan analyse --memory-limit=2G
```


### PHPStan

**Configuration**: `phpstan.neon`

Static analysis with Larastan (Laravel-specific rules):
- **Level 5** analysis (balanced strictness)
- Laravel model property checking
- Paths analyzed: `app/`, `config/`, `routes/`
- Excluded: `bootstrap/`, `storage/`, `vendor/`, `database/migrations/`

**Usage**:
```bash
# Run static analysis
./vendor/bin/phpstan analyse

# With memory limit
./vendor/bin/phpstan analyse --memory-limit=2G
```

## 🔄 CI/CD Pipeline

### GitHub Actions Workflow

**File**: `.github/workflows/ci.yml`

The CI pipeline runs automatically on:
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

### Pipeline Structure

#### Job 1: Code Quality (runs first)
**Environment**: Ubuntu latest, PHP 8.3

**Steps**:
1. Checkout code
2. Setup PHP with extensions
3. Cache Composer dependencies
4. Install Composer packages
5. **Check code style** with Pint (`--test` flag)
6. **Run PHPStan** static analysis

If code quality checks fail, the pipeline stops here ❌

#### Job 2: Tests (runs after code-quality succeeds)
**Environment**: Ubuntu latest, PHP 8.2 & 8.3 (matrix)

**Steps**:
1. Checkout code
2. Setup PHP with extensions (8.2 and 8.3 in parallel)
3. Cache Composer dependencies
4. Install Composer packages
5. Prepare Laravel application (`.env`, `key:generate`)
6. **Run PHPUnit tests** with parallel execution

### CI Pipeline Flow

```
Push/PR to main/develop
         ↓
┌─────────────────────┐
│   Code Quality      │ ← PHP 8.3 only
│   - Pint (style)    │
│   - PHPStan (types) │
└─────────────────────┘
         ↓ (if passes)
┌─────────────────────┐
│       Tests         │ ← Matrix: PHP 8.2 & 8.3
│   - Unit           │
│   - Feature        │
│   - Views          │
│   - Parallel exec  │
└─────────────────────┘
```

### Why This Structure?

1. **Fail Fast**: Code quality issues caught before expensive test runs
2. **Cache Optimization**: Dependencies cached per PHP version
3. **Matrix Testing**: Ensures compatibility with PHP 8.2 and 8.3
4. **Parallel Execution**: Faster test completion with `--parallel` flag

## 📊 Test Statistics

**Current Status** (as of implementation):
- ✅ **100 tests** passing
- ✅ **233 assertions**
- ✅ **0 failures**
- ⏱️ **~3-4 seconds** execution time

### Test Distribution:
- Unit Tests: 13 (13%)
- Feature Tests: 71 (71%)
- View Tests: 16 (16%)

## 🔧 Database Configuration

Tests use **SQLite in-memory database** for fast, isolated testing:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

**Benefits**:
- No database setup required
- Fast test execution
- Complete isolation between tests
- Automatic cleanup after each test

## 📝 Writing New Tests

### Unit Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_functionality(): void
    {
        // Arrange
        $service = YourService::create();

        // Act
        $result = $service->doSomething();

        // Assert
        $this->assertTrue($result);
    }
}
```

### Feature Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_perform_action(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/endpoint', ['data' => 'value']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('table', ['data' => 'value']);
    }
}
```

### View Test Template

```php
<?php

declare(strict_types=1);

namespace Tests\Views;

use Tests\TestCase;

class YourViewTest extends TestCase
{
    public function test_view_renders_successfully(): void
    {
        $response = $this->get(route('your.route'));

        $response->assertStatus(200);
        $response->assertViewIs('your.view');
        $response->assertSee('Expected Content');
    }
}
```

## 🎨 Code Style Guidelines

All tests must follow project standards (enforced by Pint):

### Required Standards:
1. ✅ `declare(strict_types=1);` on line 3 of every PHP file
2. ✅ All method parameters must have type hints
3. ✅ All methods must have return type declarations
4. ✅ Use `void` for methods returning nothing
5. ✅ Test methods must start with `test_` prefix
6. ✅ Test method names should be descriptive (use snake_case)

### Example:
```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_user_can_create_submission(): void
    {
        // Test implementation
    }
}
```

## 🔍 Debugging Tests

### Run Single Test
```bash
php artisan test --filter=test_method_name
```

### Show Test Output
```bash
php artisan test --testdox
```

### Verbose Mode
```bash
php artisan test -v
```

### Stop on First Failure
```bash
php artisan test --stop-on-failure
```

## 📚 Best Practices

1. **Isolation**: Each test should be independent and not rely on other tests
2. **Arrange-Act-Assert**: Structure tests in three clear sections
3. **RefreshDatabase**: Always use for tests that interact with the database
4. **Factory Pattern**: Use factories instead of manual model creation
5. **Clear Names**: Test names should describe what they're testing
6. **One Assertion Concept**: Each test should verify one logical concept
7. **Setup/Teardown**: Use `setUp()` for common test initialization

## 🐛 Troubleshooting

### Tests Failing Locally but Passing in CI
- Check PHP version: `php -v`
- Clear caches: `php artisan config:clear && php artisan cache:clear`
- Update dependencies: `composer install`

### PHPStan Errors
- Check `phpstan.neon` for excluded paths
- Run with verbose: `./vendor/bin/phpstan analyse -vvv`
- Clear result cache: `./vendor/bin/phpstan clear-result-cache`

### Pint Style Issues
- Run Pint to auto-fix: `./vendor/bin/pint`
- Check specific file: `./vendor/bin/pint path/to/file.php`

## 📦 Dependencies

### Testing Tools
- **PHPUnit**: 11.5.3 (test framework)
- **Laravel Pint**: 1.27+ (code formatter)
- **PHPStan**: 2.1+ (static analyzer)
- **Larastan**: 3.8+ (Laravel-specific PHPStan rules)

### Test Database
- SQLite (in-memory)

## 🎓 Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Guide](https://laravel.com/docs/testing)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Laravel Pint Documentation](https://laravel.com/docs/pint)

---

**Last Updated**: January 2026
**Test Coverage**: 100 tests, 233 assertions
**Maintained by**: Development Team with Claude Sonnet 4.5

### TO DO
Struktura a pojmenování testů
Momentálně v repositáři zřejmě není jasné oddělení typů testů (např. Unit vs Feature vs Views vs API). Laravel umožňuje testy rozdělit standardně:
tests/
├── Feature/
├── Unit/
├── Views/
├── Api/
├── Dusk/          // optional
Každá skupina testů by měla mít svou doménu:
Test typ	Co ověřuje
Unit	Nejmenší části logiky (metody, služby)
Feature	HTTP endpointy controllers
Views	Blade rendering a komponenty
Api	API odpovědi, JSON ověřování

Chybí jasné oddělení testových vrstev
Laravel standard doporučuje rozdělení (Unit, Feature) a případně Views/Api pokud projekt rostl.
✓ Méně dobré pojmenování
Senioři preferují test názvy, které popisují účel a podmínky, nikoli generické „example tests“.
