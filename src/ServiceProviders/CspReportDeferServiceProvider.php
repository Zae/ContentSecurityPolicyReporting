<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\ServiceProviders;

use Illuminate\Contracts\Support\DeferrableProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class CspReportServiceProvider
 *
 * @package Zae\ContentSecurityPolicyReporting\ServiceProviders
 */
class CspReportDeferServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->bind(
            CspPersistable::class,
            function (Application $app) {
                [$class, $properties] = $this->getPersistableConcrete();

                return $app->make(
                    $class,
                    $properties
                );
            }
        );

        $this->app->bind(
            CspLimiter::class,
            function (Application $app) {
                [$class, $properties] = $this->getLimiterConcrete();

                return $app->make(
                    $class,
                    $properties
                );
            }
        );

        $this->app->bind(ResponseFactoryInterface::class, Psr17Factory::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            ResponseFactoryInterface::class,
            CspPersistable::class,
            $this->getPersistableConcrete()[0],
            $this->getLimiterConcrete()[0]
        ];
    }

    /**
     * @return array
     */
    private function getPersistableConcrete(): array
    {
        return [
            $this->app->config->get('csp-report.persist.class'),
            $this->app->config->get('csp-report.persist.properties', [])
        ];
    }

    /**
     * @return array
     */
    private function getLimiterConcrete(): array
    {
        return [
            $this->app->config->get('csp-report.limiter.class'),
            $this->app->config->get('csp-report.limiter.properties', [])
        ];
    }
}
