<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests\Feature;

use Bugsnag\Client;
use Bugsnag\Report;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Zae\ContentSecurityPolicyReporting\Http\Controllers\CspReportController;
use Zae\ContentSecurityPolicyReporting\Persisters\BugsnagCspPersister;
use Zae\ContentSecurityPolicyReporting\Persisters\LogCspPersister;
use Zae\ContentSecurityPolicyReporting\Tests\LaravelTestCase;

/**
 * Class ReporterTest
 *
 * @package Zae\ContentSecurityPolicyReporting\Tests
 */
class ReporterTest extends LaravelTestCase
{
    protected function useBugsnagPersister($app)
    {
        $app->config->set('csp-report.persist', [
            'class' => BugsnagCspPersister::class,
            'properties' => [
                'loglevel' => LogLevel::INFO
            ]
        ]);
    }

    protected function useLogPersister($app)
    {
        $app->config->set('csp-report.persist', [
            'class' => LogCspPersister::class,
            'properties' => [
                'loglevel' => LogLevel::INFO
            ]
        ]);
    }

    /**
     * @test
     * @group laravel
     * @group persister
     * @group bugsnag
     *
     * @environment-setup useBugsnagPersister
     */
    public function bugsnag()
    {
        $this->mock(Client::class, static function (MockInterface $mock) {
            $mock->expects()
                ->notifyException(Mockery::any(), Mockery::on(function(\Closure $closure) {
                    $mock = Mockery::mock(Report::class);

                    $mock->expects()
                         ->addMetaData(Mockery::any())
                         ->once();

                    $mock->expects()
                         ->setSeverity(LogLevel::INFO)
                         ->once();

                    $closure($mock);

                    return true;
                }))
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
    }

    /**
     * @test
     * @group laravel
     * @group persister
     * @group log
     *
     * @environment-setup useLogPersister
     */
    public function log()
    {
        $this->mock(LoggerInterface::class, static function (MockInterface $mock) {
            $mock->expects()
                ->info(Mockery::any())
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
    }
}
