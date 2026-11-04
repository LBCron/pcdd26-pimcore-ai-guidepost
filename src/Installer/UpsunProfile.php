<?php

declare(strict_types=1);

namespace App\Installer;

use Pimcore\Bundle\InstallBundle\Profile\DataSource\DataSourceInterface;
use Pimcore\Bundle\InstallBundle\Profile\InstallProfileInterface;
use Pimcore\Bundle\InstallBundle\Profile\InstallStep;
use Pimcore\Bundle\InstallBundle\Profile\InstallStepFilterInterface;

/**
 * SkeletonProfile for Upsun (and other PaaS): identical install, but skips
 * writing .env.local because the application filesystem is read-only and
 * every collected value is already provided as a real environment variable
 * (Upsun project variables + .environment mappings).
 */
final readonly class UpsunProfile implements InstallProfileInterface, InstallStepFilterInterface
{
    private SkeletonProfile $inner;

    public function __construct()
    {
        $this->inner = new SkeletonProfile();
    }

    public function getName(): string
    {
        return $this->inner->getName() . ' (Upsun)';
    }

    public function getDescription(): string
    {
        return $this->inner->getDescription() . ' Env writing skipped: values come from PaaS environment variables.';
    }

    public function getBundles(): array
    {
        return $this->inner->getBundles();
    }

    public function getEnvVarDefinitions(): array
    {
        return $this->inner->getEnvVarDefinitions();
    }

    public function getDataSource(): ?DataSourceInterface
    {
        return $this->inner->getDataSource();
    }

    public function getPostInstallCommands(): array
    {
        return $this->inner->getPostInstallCommands();
    }

    public function getSkippedInstallSteps(): array
    {
        return [InstallStep::WriteEnv];
    }
}
