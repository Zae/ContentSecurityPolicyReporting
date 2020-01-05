<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests\Feature;

use Mockery;
use Mockery\MockInterface;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Zae\ContentSecurityPolicyReporting\Http\Controllers\CspReportController;
use Zae\ContentSecurityPolicyReporting\Tests\LaravelTestCase;

/**
 * Class LaravelControllerTest
 *
 * @package Zae\ContentSecurityPolicyReporting\Tests
 */
class LaravelControllerTest extends LaravelTestCase
{
    /**
     * @test
     * @group laravel
     * @group limiter
     */
    public function it_persist_when_not_limited()
    {
        $this->app->make('router')->post('/csp-report', [
            'uses' => CspReportController::class . '@report',
            'as' => 'csp.report'
        ]);

        $this->mock(CspLimiter::class, static function (MockInterface $mock) {
            $mock->expects()
                ->tooManyAttempts()
                ->once()
                ->andReturn(false);

            $mock->expects()
                ->hit()
                ->once();
        });

        $this->mock(CspPersistable::class, static function (MockInterface $mock) {
            $mock->expects()
                ->persist(Mockery::any())
                ->once();
        });

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
     */
    public function it_does_not_persist_when_limited()
    {
        $this->app->make('router')->post('/csp-report', [
            'uses' => CspReportController::class . '@report',
            'as' => 'csp.report'
        ]);

        $this->mock(CspLimiter::class, static function (MockInterface $mock) {
            $mock->expects()
                ->tooManyAttempts()
                ->once()
                ->andReturn(true);

            $mock->expects()
                ->hit()
                ->never();
        });

        $this->mock(CspPersistable::class, static function (MockInterface $mock) {
            $mock->expects()
                ->persist(Mockery::any())
                ->never();
        });

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
