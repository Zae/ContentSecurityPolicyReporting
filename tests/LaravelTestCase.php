<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests;

use Orchestra\Testbench\TestCase;
use Zae\ContentSecurityPolicyReporting\ServiceProviders\CspReportBootServiceProvider;
use Zae\ContentSecurityPolicyReporting\ServiceProviders\CspReportDeferServiceProvider;

class LaravelTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            CspReportDeferServiceProvider::class,
            CspReportBootServiceProvider::class
        ];
    }
}
