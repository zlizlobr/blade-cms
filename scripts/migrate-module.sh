#!/bin/bash

###############################################################################
# Module Migration Script
# Converts a module from app/Modules/ to a standalone Composer repository
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if module name is provided
if [ -z "$1" ]; then
    print_error "Usage: ./scripts/migrate-module.sh <ModuleName>"
    print_info "Example: ./scripts/migrate-module.sh Blog"
    exit 1
fi

MODULE_NAME=$1
MODULE_PATH="app/Modules/${MODULE_NAME}"
TARGET_DIR="../blade-modules"
MODULE_NAME_LOWER=$(echo "${MODULE_NAME}" | tr '[:upper:]' '[:lower:]')
MODULE_REPO="${TARGET_DIR}/blade-module-${MODULE_NAME_LOWER}"

# Check if module exists
if [ ! -d "$MODULE_PATH" ]; then
    print_error "Module '${MODULE_NAME}' not found in ${MODULE_PATH}"
    exit 1
fi

# Check if module.json exists
if [ ! -f "${MODULE_PATH}/module.json" ]; then
    print_error "module.json not found in ${MODULE_PATH}"
    exit 1
fi

print_info "Starting migration of ${MODULE_NAME} module..."

# Step 1: Create target directory
print_info "Step 1/8: Creating repository directory..."
mkdir -p "${TARGET_DIR}"

# Step 2: Initialize new Git repository
print_info "Step 2/8: Initializing Git repository..."
if [ -d "$MODULE_REPO" ]; then
    print_warning "Repository ${MODULE_REPO} already exists. Skipping git init."
else
    git init "${MODULE_REPO}"
    cd "${MODULE_REPO}"
    git checkout -b main
    cd - > /dev/null
fi

# Step 3: Copy module files
print_info "Step 3/8: Copying module files..."
rsync -av --exclude='.git' "${MODULE_PATH}/" "${MODULE_REPO}/src-temp/"

# Step 4: Restructure for Composer package
print_info "Step 4/8: Restructuring for Composer package..."
cd "${MODULE_REPO}"

# Move files to proper structure
mkdir -p src routes resources/views database/migrations config

if [ -d "src-temp/Controllers" ]; then
    mv src-temp/Controllers src/ || true
fi

if [ -d "src-temp/Models" ]; then
    mv src-temp/Models src/ || true
fi

if [ -d "src-temp/Providers" ]; then
    mv src-temp/Providers src/ || true
fi

if [ -d "src-temp/Routes" ]; then
    mv src-temp/Routes/* routes/ || true
fi

if [ -d "src-temp/Views" ]; then
    mv src-temp/Views/* resources/views/ || true
fi

if [ -d "src-temp/Migrations" ]; then
    mv src-temp/Migrations/* database/migrations/ || true
fi

if [ -d "src-temp/Config" ]; then
    mv src-temp/Config/* config/ || true
fi

if [ -f "src-temp/module.json" ]; then
    mv src-temp/module.json .
fi

rm -rf src-temp

cd - > /dev/null

# Step 5: Create composer.json
print_info "Step 5/10: Creating composer.json..."
MODULE_SLUG=$(cat "${MODULE_PATH}/module.json" | grep -o '"slug"[^,]*' | cut -d'"' -f4)
MODULE_DESCRIPTION=$(cat "${MODULE_PATH}/module.json" | grep -o '"description"[^}]*' | cut -d'"' -f4)

cat > "${MODULE_REPO}/composer.json" <<EOF
{
    "name": "bladecms/module-${MODULE_SLUG}",
    "description": "${MODULE_DESCRIPTION}",
    "type": "library",
    "license": "MIT",
    "version": "0.0.1",
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0",
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^2.0",
        "laravel/pint": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "App\\\\Modules\\\\${MODULE_NAME}\\\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "App\\\\Modules\\\\${MODULE_NAME}\\\\Providers\\\\ModuleServiceProvider"
            ]
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
EOF

# Step 6: Create README
print_info "Step 6/10: Creating README.md..."
cat > "${MODULE_REPO}/README.md" <<EOF
# ${MODULE_NAME} Module

${MODULE_DESCRIPTION}

## Installation

Add this to your CMS \`composer.json\`:

\`\`\`json
{
    "require": {
        "bladecms/module-${MODULE_SLUG}": "^${MODULE_VERSION}"
    }
}
\`\`\`

For local development:

\`\`\`json
{
    "repositories": [
        {
            "type": "path",
            "url": "../blade-modules/blade-module-${MODULE_SLUG}",
            "options": {
                "symlink": true
            }
        }
    ]
}
\`\`\`

Then run:

\`\`\`bash
composer require bladecms/module-${MODULE_SLUG}
\`\`\`

## Activation

Via Tinker:

\`\`\`php
\$service = app(\App\Domain\Module\Services\ModuleServiceInterface::class);
\$service->install('${MODULE_SLUG}', [
    'name' => '${MODULE_NAME}',
    'slug' => '${MODULE_SLUG}',
    'version' => '${MODULE_VERSION}',
]);
\$service->activate('${MODULE_SLUG}');
\`\`\`

Or via Admin UI: \`/admin/modules\`

## License

MIT
EOF

# Step 7: Setup CI/CD and testing
print_info "Step 7/10: Setting up CI/CD workflows..."
mkdir -p "${MODULE_REPO}/.github/workflows"

if [ -f ".github/module-templates/workflows/ci.yml" ]; then
    cp .github/module-templates/workflows/ci.yml "${MODULE_REPO}/.github/workflows/"
fi

if [ -f ".github/module-templates/workflows/release.yml" ]; then
    cp .github/module-templates/workflows/release.yml "${MODULE_REPO}/.github/workflows/"
fi

# Step 8: Setup testing files
print_info "Step 8/10: Setting up testing infrastructure..."

if [ -f ".github/module-templates/phpunit.xml" ]; then
    cp .github/module-templates/phpunit.xml "${MODULE_REPO}/"
fi

if [ -f ".github/module-templates/phpstan.neon" ]; then
    cp .github/module-templates/phpstan.neon "${MODULE_REPO}/"
fi

if [ -f ".github/module-templates/CHANGELOG.md" ]; then
    cp .github/module-templates/CHANGELOG.md "${MODULE_REPO}/"
fi

mkdir -p "${MODULE_REPO}/tests/Unit"
mkdir -p "${MODULE_REPO}/tests/Feature"

if [ -f ".github/module-templates/TestCase.php.stub" ]; then
    cp .github/module-templates/TestCase.php.stub "${MODULE_REPO}/tests/TestCase.php"
    # Replace {ModuleName} placeholder
    if [[ "$OSTYPE" == "darwin"* ]]; then
        sed -i '' "s/{ModuleName}/${MODULE_NAME}/g" "${MODULE_REPO}/tests/TestCase.php"
    else
        sed -i "s/{ModuleName}/${MODULE_NAME}/g" "${MODULE_REPO}/tests/TestCase.php"
    fi
fi

if [ -f ".github/module-templates/ExampleTest.php.stub" ]; then
    cp .github/module-templates/ExampleTest.php.stub "${MODULE_REPO}/tests/Feature/ExampleTest.php"
fi

# Step 9: Initial commit
print_info "Step 9/10: Creating initial commit..."
cd "${MODULE_REPO}"

# Always start with v0.0.1 for new standalone modules
INITIAL_VERSION="v0.0.1"

git add .
git commit -m "Initial commit: ${MODULE_NAME} module ${INITIAL_VERSION}

Migrated from monorepo to standalone Composer package.

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"

git tag -a "${INITIAL_VERSION}" -m "Initial release ${INITIAL_VERSION}"
cd - > /dev/null

# Step 10: Update CMS composer.json
print_info "Step 10/10: Updating CMS composer.json..."
print_warning "You need to manually add this to your CMS composer.json require section:"
print_info "\"bladecms/module-${MODULE_SLUG}\": \"@dev\""

print_info ""
print_info "✅ Migration complete!"
print_info ""
print_info "Next steps:"
print_info "1. cd ${MODULE_REPO} && git remote add origin <your-git-url>"
print_info "2. Add to CMS composer.json: \"bladecms/module-${MODULE_SLUG}\": \"@dev\""
print_info "3. Run: composer require bladecms/module-${MODULE_SLUG}"
print_info "4. Remove old module: rm -rf ${MODULE_PATH}"
print_info "5. Test the module in CMS"
print_info ""
print_info "Repository location: ${MODULE_REPO}"
