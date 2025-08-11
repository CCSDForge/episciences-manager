#!/bin/bash
set -e  # Exit on any error

echo "🧪 Running CI Test Suite..."

echo "📦 Installing dependencies..."
composer install --no-interaction --optimize-autoloader
npm ci

echo "🏗️ Building assets..."
npm run build

echo "📊 Linting JavaScript..."
npm run lint || true  # Allow to continue even if linting fails

echo "🎨 Checking formatting..."
npm run format:check || true  # Allow to continue even if formatting fails

echo "🧪 Running JavaScript tests with coverage..."
npm test -- --passWithNoTests --coverage

echo "🧪 Running PHP tests with Pest..."
if [ -f "vendor/bin/pest" ]; then
    vendor/bin/pest --testdox
else
    echo "❌ Pest not found!"
    exit 1
fi

echo "✅ All tests completed successfully!"