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

