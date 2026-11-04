#!/bin/bash
# Upsun deploy hook: install Pimcore on first boot of an environment,
# run migrations afterwards. Idempotent by design: preview environments
# that clone production data skip the install.
set -euo pipefail

if php bin/console doctrine:query:sql "SELECT id FROM users LIMIT 1" >/dev/null 2>&1; then
  echo "Pimcore already installed: running migrations and cache warmup."
  php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
  php bin/console cache:clear
else
  echo "Fresh environment: running pimcore-install."
  vendor/bin/pimcore-install --install-profile='App\Installer\SkeletonProfile' --no-interaction
fi
