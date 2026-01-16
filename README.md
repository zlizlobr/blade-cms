# Blade CMS - Laravel SaaS MVP

A modern **multi-tenant SaaS application** built with Laravel 11, featuring form submission management, tenant isolation, and a comprehensive admin panel.

## 🚀 Features

- **Multi-Tenant Architecture** - Single database with automatic tenant scoping
- **Form Submission System** - Contact forms with event-driven notifications
- **Admin Panel** - Dashboard with statistics, submission management
- **Extensible Admin Sidebar** - Registry-based sidebar with groups, permissions, and caching
- **Domain-Driven Design** - Clean architecture with separated concerns
- **Strict Type Safety** - PHP 8.1+ strict types across entire codebase
- **Dependency Injection** - Constructor injection with static factory methods
- **Interface-Based Design** - Program to abstractions, not implementations
- **Role-Based Access Control** - Admin and Subscriber roles
- **Event-Driven Architecture** - Queued listeners for scalability
- **Repository Pattern** - Abstracted data access layer
- **RESTful API** - JSON responses for AJAX requests
- **Progressive Enhancement** - Works with or without JavaScript
- **Internationalization (i18n)** - Czech and English support with automatic locale detection

## 📋 Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+ & NPM
- SQLite (or MySQL/PostgreSQL)

## 🛠️ Installation

### 1. Clone the repository

\`\`\`bash
git clone <repository-url>
cd blade-cms
\`\`\`

### 2. Install dependencies

\`\`\`bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
\`\`\`

### 3. Environment setup

\`\`\`bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
\`\`\`

### 4. Database setup

\`\`\`bash
# Run migrations
php artisan migrate

# (Optional) Seed with test data
php artisan db:seed
\`\`\`

### 5. Build assets

\`\`\`bash
# Development build
npm run dev

# Production build
npm run build
\`\`\`

### 6. Start development server

\`\`\`bash
# Start Laravel server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
\`\`\`

Visit: \`http://localhost:8000\`

## 🔐 Test Credentials

After running \`php artisan db:seed\`, you can login with:

**Admin Account:**
- Email: \`admin@example.com\`
- Password: \`password\`

**Subscriber Account:**
- Email: \`subscriber@example.com\`
- Password: \`password\`

## 📁 Project Structure

\`\`\`
blade-cms/
├── app/
│   ├── Domain/                      # Domain Layer (Business Logic)
│   │   ├── User/
│   │   │   ├── Models/              # User, Role, Status
│   │   │   ├── Enums/               # UserRole, UserStatus
│   │   │   └── Policies/            # Authorization policies
│   │   ├── Tenant/
│   │   │   ├── Models/              # Tenant model
│   │   │   └── Enums/               # PlanType, TenantStatus
│   │   ├── FormSubmission/
│   │   │   ├── Models/              # FormSubmission model
│   │   │   ├── Services/            # Business logic services
│   │   │   ├── Repositories/        # Data access layer
│   │   │   ├── Events/              # Domain events
│   │   │   └── Listeners/           # Event listeners
│   │   └── Dashboard/
│   │       └── Services/            # Dashboard statistics
│   ├── Presentation/                # Presentation Layer (HTTP)
│   │   └── Http/
│   │       ├── Controllers/
│   │       │   ├── Admin/           # Admin controllers
│   │       │   └── Web/             # Public controllers
│   │       ├── Requests/            # Form requests
│   │       └── Middleware/          # Custom middleware
│   ├── Infrastructure/              # Infrastructure Layer
│   │   └── Providers/               # Service providers
│   └── Support/                     # Support Layer
│       └── Traits/                  # Reusable traits
├── database/
│   ├── migrations/                  # Database migrations
│   ├── factories/                   # Model factories
│   └── seeders/                     # Database seeders
├── resources/
│   ├── views/                       # Blade templates
│   │   ├── layouts/                 # Layout files
│   │   ├── partials/                # Reusable partials
│   │   └── admin/                   # Admin views
│   └── js/                          # JavaScript files
├── routes/
│   ├── web.php                      # Web routes
│   └── auth.php                     # Authentication routes
├── tests/
│   └── Feature/                     # Feature tests
└── docs/                            # Documentation
    ├── PHASE_*.md                   # Phase documentation
    └── ARCHITECTURE.md              # Architecture overview
\`\`\`

## 🎯 Key Concepts

### Multi-Tenant Architecture

This application uses a **single-database multi-tenancy** strategy:

- Each model has a \`tenant_id\` foreign key
- \`BelongsToTenant\` trait provides automatic scoping
- \`SetTenantContext\` middleware sets tenant from user session
- All queries are automatically scoped to current tenant

### Domain-Driven Design

The codebase follows DDD principles with clear layer separation:

- **Domain Layer**: Business logic, models, services, repositories
- **Presentation Layer**: Controllers, requests, views
- **Infrastructure Layer**: Providers, external integrations
- **Support Layer**: Cross-cutting concerns, helpers

### Modern PHP Practices

This project implements modern PHP 8.1+ features and patterns:

**Strict Type Safety:**
```php
<?php

declare(strict_types=1);

namespace App\Domain\FormSubmission\Services;

class FormSubmissionService implements FormSubmissionServiceInterface
{
    public function createSubmission(array $data): FormSubmission
    {
        // All parameters and return types are strictly enforced
    }
}
```

**Dependency Injection with Factory Methods:**
```php
class BookingService
{
    // Constructor injection
    public function __construct(
        private readonly BookingRepositoryInterface $repository
    ) {}

    // Static factory for convenience
    public static function create(): self
    {
        return new self(new BookingRepository());
    }
}
```

**Interface-Based Design:**
```php
// Controllers depend on interfaces, not implementations
class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardServiceInterface $service
    ) {}
}
```

All services implement interfaces, making the codebase highly testable and maintainable.

### Event-Driven Architecture

Form submissions trigger events that are processed asynchronously:

\`\`\`php
// Event is fired
event(new FormSubmitted(\$submission));

// Listener processes it (queued)
class SendFormNotification implements ShouldQueue
{
    public function handle(FormSubmitted \$event) { }
}
\`\`\`

## 🧪 Testing

\`\`\`bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/FormSubmissionTest.php

# Run with coverage
php artisan test --coverage
\`\`\`

## 📝 Development Commands

\`\`\`bash
# Code formatting (Laravel Pint)
./vendor/bin/pint

# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Queue worker (for development)
php artisan queue:work

# Database fresh migration with seeding
php artisan migrate:fresh --seed
\`\`\`

## 🔧 Configuration

### Environment Variables

Key environment variables to configure:

\`\`\`env
APP_NAME="Blade CMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=blade_cms
# DB_USERNAME=root
# DB_PASSWORD=

QUEUE_CONNECTION=database
MAIL_MAILER=log
\`\`\`

### Cache Configuration

\`\`\`bash
# Cache routes (production only)
php artisan route:cache

# Cache config (production only)
php artisan config:cache

# Cache views
php artisan view:cache
\`\`\`

## 📚 Documentation

### Core Documentation

- [TASK.md](TASK.md) - Complete refactoring task documentation (Strict Types, DI, Service Layer)
- [Claude Code Rules](.claude/claude.md) - Development guidelines and coding standards
- [Architecture Overview](docs/ARCHITECTURE.md) - System architecture details
- [Internationalization](docs/INTERNATIONALIZATION.md) - i18n implementation guide
- [Admin Sidebar Guide](docs/ADMIN_SIDEBAR_GUIDE.md) - Extensible sidebar registry documentation

### Phase Documentation

Detailed documentation for each development phase:

- [Phase 1: Core SaaS](docs/PHASE_1_CORE_SAAS.md) - User management, roles, authentication
- [Phase 2: Tenant Architecture](docs/PHASE_2_TENANT_ARCHITECTURE.md) - Multi-tenancy setup
- [Phase 3: Form Submissions](docs/PHASE_3_FORM_SUBMISSIONS.md) - Form system implementation
- [Phase 4: Marketing Page](docs/PHASE_4_MARKETING_PAGE.md) - Public-facing pages
- [Phase 5: Admin MVP](docs/PHASE_5_ADMIN_MVP.md) - Admin panel

### Claude Code Integration

This project includes configuration for [Claude Code](https://claude.ai/code):

- `.claude/claude.md` - Project-specific coding standards and architecture rules
- `TASK.md` - Refactoring task documentation and workflow

When working with Claude Code, these rules are automatically applied to ensure consistent code quality and adherence to project standards.

## 🤝 Contributing

This is a learning project demonstrating best practices for Laravel SaaS applications.

### Code Style

The project follows strict coding standards:

- **PSR-12** coding style
- **Strict types** (`declare(strict_types=1);`) in all PHP files
- **Full type hints** on all parameters and return values
- **Interface-based design** for all services
- **Constructor injection** for dependencies

Run Pint before committing:

\`\`\`bash
./vendor/bin/pint
\`\`\`

See [`.claude/claude.md`](.claude/claude.md) for complete coding standards.

### Git Workflow

\`\`\`bash
# Create feature branch
git checkout -b feature/your-feature-name

# Make changes and commit
git add .
git commit -m "feat: description"

# Push to remote
git push origin feature/your-feature-name
\`\`\`

## 🐛 Troubleshooting

### Common Issues

**Issue: "Target class [tenant.id] does not exist"**

Solution: Ensure \`bootstrap/providers.php\` exists and contains all service providers.

\`\`\`bash
php artisan config:clear
php artisan route:clear
\`\`\`

**Issue: "Class 'App\\Models\\User' not found"**

Solution: Update imports to use \`App\\Domain\\User\\Models\\User\`.

**Issue: Routes not working**

Solution: Clear route cache:

\`\`\`bash
php artisan route:clear
php artisan optimize:clear
\`\`\`

### Debug Mode

Enable detailed error messages:

\`\`\`env
APP_DEBUG=true
LOG_LEVEL=debug
\`\`\`

Then check logs:

\`\`\`bash
tail -f storage/logs/laravel.log
\`\`\`

## 📈 Performance Tips

### Production Optimization

\`\`\`bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize images
npm run build
\`\`\`

### Database Optimization

Add indexes for frequently queried columns:

\`\`\`php
// In migrations
\$table->index('tenant_id');
\$table->index(['tenant_id', 'created_at']);
\`\`\`

### Queue Workers

Use supervisord or similar to keep queue workers running:

\`\`\`bash
php artisan queue:work --tries=3
\`\`\`

## 🔒 Security

- ✅ CSRF protection on all forms
- ✅ SQL injection prevention via Query Builder
- ✅ XSS protection via Blade escaping
- ✅ Tenant isolation enforced globally
- ✅ Role-based access control
- ✅ Password hashing with bcrypt

### Security Checklist for Production

- [ ] Set \`APP_DEBUG=false\`
- [ ] Use HTTPS only
- [ ] Configure proper CORS settings
- [ ] Enable rate limiting
- [ ] Set secure session cookies
- [ ] Configure Content Security Policy
- [ ] Regular dependency updates

## 📄 License

This project is open-source software licensed under the MIT license.

## 🙏 Acknowledgments

Built with:
- [Laravel 11](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [Vite](https://vitejs.dev)

---

## 🏗️ Architecture Refactoring

This project has undergone a comprehensive refactoring to implement modern PHP practices:

### Completed Phases

✅ **Phase 1: Strict Typing** - All PHP files use `declare(strict_types=1);` with full type hints
✅ **Phase 2: Dependency Injection** - Constructor injection with static factory methods
✅ **Phase 3: Service Layer** - Interface-based design with clean separation of concerns
✅ **Phase 4: Admin Sidebar Registry** - Extensible sidebar with groups, permissions, caching

See [TASK.md](TASK.md) for detailed refactoring documentation and workflow.

---

**Version:** 1.0.0
**Last Updated:** January 2026
**Maintained by:** Development Team
