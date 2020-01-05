<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests\Feature;

use Bugsnag\Client;
use Illuminate\Cache\RateLimiter;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Zae\ContentSecurityPolicyReporting\Http\Controllers\CspReportController;
use Zae\ContentSecurityPolicyReporting\Limiters\CspCacheLimiter;
use Zae\ContentSecurityPolicyReporting\Limiters\CspLotteryLimiter;
use Zae\ContentSecurityPolicyReporting\Persisters\BugsnagCspPersister;
use Zae\ContentSecurityPolicyReporting\Persisters\LogCspPersister;
use Zae\ContentSecurityPolicyReporting\Tests\LaravelTestCase;

/**
 * Class LimiterTest
 *
 * @package Zae\ContentSecurityPolicyReporting\Tests
 */
class LimiterTest extends LaravelTestCase
{
    protected function useCacheRateLimiter($app)
    {
        $app->config->set('csp-report.limiter', [
            'class' => CspCacheLimiter::class,
            'properties' => [
                'key' => 'csp-rate-limiter',
                'maxAttempts' => 2,
                'decay' => 60
            ]
        ]);
    }

    protected function useRandomLimiter($app)
    {
        $app->config->set('csp-report.limiter', [
            'class' => CspLotteryLimiter::class,
            'properties' => [
                'chance' => 100
            ]
        ]);

        $app->config->set('csp-report.persist', [
            'class' => LogCspPersister::class,
        ]);
    }

    /**
     * @test
     * @group laravel
     * @group limiter
     * @group cache
     *
     * @environment-setup useCacheRateLimiter
     */
    public function cachelimiter()
    {
        $this->mock(RateLimiter::class, static function (MockInterface $mock) {
            $mock->expects()
                ->tooManyAttempts('csp-rate-limiter', 2)
                ->twice()
                ->andReturn(false, true);

            $mock->expects()
                ->hit('csp-rate-limiter', 60)
                ->once();
        });

        $this->app->make('router')->post('/csp-report', [
            'uses' => CspReportController::class . '@report',
            'as' => 'csp.report'
        ]);

        $this->postJson(route('csp.report'), [
            'csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]
        ])->assertStatus(202);

        $this->postJson(route('csp.report'), [
            'csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]
        ])->assertStatus(202);
    }

    /**
     * @test
     * @group laravel
     * @group limiter
     * @group random
     *
     * @environment-setup useRandomLimiter
     */
    public function random()
    {
        $this->mock(LogCspPersister::class, static function (MockInterface $mock) {
            $mock->expects()
                ->persist(Mockery::any())
                ->once();
        });

        $this->app->make('router')->post('/csp-report', [
            'uses' => CspReportController::class . '@report',
            'as' => 'csp.report'
        ]);

        $this->postJson(route('csp.report'), [
            'csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]
        ])->assertStatus(202);

        $this->app->config->set('csp-report.limiter', [
            'class' => CspLotteryLimiter::class,
            'properties' => [
                'chance' => 0
            ]
        ]);

        $this->postJson(route('csp.report'), [
            'csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]
        ])->assertStatus(202);
    }
}
