## [v0.9.0] – 2026-01-16

- release: minor -- Add extensible admin sidebar with registry pattern (#20)
* Add extensible admin sidebar with registry pattern

* Add sidebar groups, active state, caching and unit tests

* Update README with new feature and documentation link
- chore: update changelog for v0.8.0

## [v0.8.0] – 2026-01-14

- release: minor -- Add CI/CD workflows and testing infrastructure for standalone modules (#19)
* Composer Path Repositoriesfor modules

* Configure Composer path repositories for modular architecture

* Add CI/CD templates and testing infrastructure for modules
- chore: update changelog for v0.7.1

## [v0.7.1] – 2026-01-13

- release: patch -- Add docs for modules (#18)
* Add docs for modules
- chore: update changelog for v0.7.0

## [v0.7.0] – 2026-01-13

- release: minor -- Core Module System (#17)
* Add core Domain layer for Module system with dependencies and versioning

* Add Module events, listeners and Infrastructure layer with hot-reload

* Add unit tests for VersionChecker and DependencyResolver services

* Add comprehensive feature tests for module lifecycle management

* Admin UI

* Add module documentation and example Blog module with lifecycle tests

* Add REST API endpoints for module management with authentication and tests

* Fix Suppress PHP 8.5 PDO deprecation warnings from vendor files
- chore: update changelog for v0.6.5

## [v0.6.5] – 2026-01-13

- release: patch -- Actions CI workflow: unit tests (#16)
* Add Views and Api test suites to PHPUnit configuration

* Add GitHub Actions CI workflow for automated testing

* Add Laravel Pint configuration with strict types enforcement

* Add PHPStan with Larastan for static code analysis

* Add code quality checks to CI workflow with Pint and PHPStan

* Format authentication tests with strict types declaration

* Add authorization and policy tests with strict types

* Add Blade view rendering tests for admin, auth, public and components

* Add unit tests for core services and remove example tests

* Downgrade to Laravel 11 for PHP 8.2-8.3 compatibility
- chore: update changelog for v0.6.4

## [v0.6.4] – 2026-01-12

- release: patch -- Refactor views to use  (#15)
* Add theme system directory structure for view separation

* Move shared view components to default theme directory

* Add ThemeViewServiceProvider to register theme namespace for view resolution

* Update view references to use theme namespace for layouts and partials

* Add comprehensive documentation for view system and theme architecture

* Move profile views to theme namespace for consistent architecture

* Convert auth views to use @extends pattern for consistency with admin views

* Create specialized welcome layout and integrate welcome page into theme system

* Refactor dashboard and profile views to use @extends pattern for consistency

* Move navigation from layouts to partials for better semantic organization

* Register admin and public view namespaces in ThemeViewServiceProvider

* Move dashboard view to admin/dashboard/index and update controller to use admin namespace

* Rename ThemeViewServiceProvider to ViewServiceProvider for better semantics

* Update SubmissionController to use admin namespace for views

* Migrate public views to dedicated public namespace and create main layout

* Migrate auth views to use dedicated auth layouts folder without namespace

* Fix profile view to use admin namespace and correct partial references

* Clean up unused theme layouts and fix admin sidebar references

* Update views README with new admin and public namespace architecture

* Fix test assertions for new view namespace architecture and add dashboard route alias

* Update view assertions in AdminTest to use correct namespace format (admin::) and add backward-compatible dashboard route for Laravel Breeze auth controllers

* Fix all remaining test failures after view refactoring

* fixed the registration flow issue
- chore: update changelog for v0.6.3

## [v0.6.3] – 2026-01-11

- release: patch -- Refactoring (#13)
* Add static factory methods to service classes for convenient instantiation outside Laravel container

* Add strict type declarations to all PHP files in the application layer

* Add service interfaces and update controllers to depend on abstractions for improved testability

* Update README with refactoring documentation and modern PHP practices showcase
- chore: update changelog for v0.6.2

## [v0.6.2] – 2026-01-11

- release: patch -- Internationalization (i18n) (#12)
* Add i18n configuration and language structure

* Implement locale detection middleware and language switcher

* Add language switcher component to UI

* Refactor public pages to use translation keys

* Refactor authentication pages to use translations

* Add multilingual support to Tenant model

* Refactor admin interface to use translations

* Add comprehensive i18n feature tests

* Add i18n feature to README feature list
- chore: update changelog for v0.6.1

## [v0.6.1] – 2026-01-10

- release: patch -- Stabilization and technical debt (#11)
* Feature tests for critical flow

* README and documentation
- chore: update changelog for v0.6.0

## [v0.6.0] – 2026-01-10

- release: minor -- admin dashboard (#10)
* Admin dashboard overview

* List of submitted forms, Detail view of submission payload
- chore: update changelog for v0.5.0

## [v0.5.0] – 2026-01-09

- release: minor -- Admin MVP (#9)
* Admin routes and layout
- chore: update changelog for v0.4.0

## [v0.4.0] – 2026-01-09

- release: minor -- Marketing page (#8)
* Blade layout and basic structure

* Home page with contact form

* Frontend connection and AJAX submission
- chore: update changelog for v0.3.0

## [v0.3.0] – 2026-01-09

- release: minor -- Form submissions (#7)
* FormSubmission model and migration

* Form submission repository pattern

* chore: ignore */cache files except .gitignore

* chrome: ignore */database.sqlite files except .gitignore

* Contact form endpoint and validation

* Domain event FormSubmitted
- chore: update changelog for v0.2.0

## [v0.2.0] – 2026-01-09

- release: minor -- Tenant architecture (#6)
* Tenant model, enumerations, and migrations

* Pivot table tenant_user

* Tenant scope middleware and trait

* Database seeders for test data.
- chore: update changelog for v0.1.2

## [v0.1.2] – 2026-01-09

- release: patch -- User (#5)
* feature: Auth scaffolding for adminy

* Update release, add changelog

* Role-based policies
- chore: update changelog for v0.1.1

## [v0.1.1] – 2026-01-08

- release: patch -- add workflow, changelog (#4)
* Update release, add changelog
- feature: Auth scaffolding for adminy
feature: Auth scaffolding for adminy
- Merge pull request #2 from zlizlobr/chore/config
release: major -- base config, User model, Enums and migration with roles
- Merge branch 'main' into chore/config

- chore: User model, Enums and migration with roles

- chore: Database connection and basic configuration

- chore: laravel config, finish 0.1 task

- chore: update laravel config

- chore: update .gitignore and add .env.example for SaaS config
- Add standard Laravel ignore patterns
- Add IDE files to .gitignore
- Create .env.example with multi-tenant configuration

