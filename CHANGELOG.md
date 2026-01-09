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

