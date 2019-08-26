<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\ServiceProviders;

use Illuminate\Support\ServiceProvider;

/**
 * Class CspReportServiceProvider
 *
 * @package Zae\ContentSecurityPolicyReporting\ServiceProviders
 */
class CspReportBootServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/csp-report.php',
            'csp-report'
        );
        $this->publishes([
            __DIR__ . '/../../config/csp-report.php' => config_path('csp-report.php')
        ]);
    }
}
