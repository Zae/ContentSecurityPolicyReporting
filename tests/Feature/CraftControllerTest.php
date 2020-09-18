<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests\Feature;

use craft\web\Request;
use craft\web\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use yii\base\Module;
use yii\di\Container;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Zae\ContentSecurityPolicyReporting\Http\Controllers\CraftController;
use Zae\ContentSecurityPolicyReporting\Tests\CraftStub;
use Zae\ContentSecurityPolicyReporting\Tests\Traits\YiiBaseMock;
use function class_exists;

/**
 * Class CraftControllerTest
 *
 * @package Zae\ContentSecurityPolicyReporting\Tests
 */
class CraftControllerTest extends TestCase
{
    use YiiBaseMock;

    protected function tearDown(): void
    {
        if (class_exists(Mockery::class)) {
            if ($container = Mockery::getContainer()) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            Mockery::close();
        }

        parent::tearDown();
    }

    /**
     * @test
     * @group craft
     * @group controller
     */
    public function it_persist_when_not_limited()
    {
        $limiterMock = Mockery::mock(CspLimiter::class);
        $limiterMock->expects()
                ->tooManyAttempts()
                ->once()
                ->andReturn(false);

        $limiterMock->expects()
                ->hit()
                ->once();

        $persisterMock = Mockery::mock(CspPersistable::class);
        $persisterMock->expects()
                      ->persist(Mockery::any())
                      ->once();

        [$requestMock, $responseMock] = $this->getYiiBaseMocks();

        $requestMock->expects()
            ->getRawBody()
            ->once()
            ->andReturn(json_encode(['csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]]));

        $responseMock->expects()
            ->setStatusCode(202)
            ->once();

        $module = new Module('csp-reporter');
        $module->set(CspPersistable::class, $persisterMock);
        $module->set(CspLimiter::class, $limiterMock);

        $controller = new CraftController(2, $module, []);
        $controller->actionIndex();
    }

    /**
     * @test
     * @group craft
     * @group controller
     */
    public function it_does_not_persist_when_limited()
    {
        $limiterMock = Mockery::mock(CspLimiter::class);
        $limiterMock->expects()
                ->tooManyAttempts()
                ->once()
                ->andReturn(true);

        $limiterMock->expects()
                ->hit()
                ->never();

        $persisterMock = Mockery::mock(CspPersistable::class);
        $persisterMock->expects()
                      ->persist(Mockery::any())
                      ->never();

        [$requestMock, $responseMock] = $this->getYiiBaseMocks();

        $requestMock->expects()
            ->getRawBody()
            ->once()
            ->andReturn(json_encode(['csp-report' => [
                'blocked-uri' => '/',
                'disposition' => '',
                'document-uri' => '',
                'effective-directive' => '',
                'original-policy' => '',
                'referrer' => '',
                'script-sample' => '',
                'status-code' => '',
                'violated-directive' => ''
            ]]));

        $responseMock->expects()
            ->setStatusCode(202)
            ->once();

        $module = new Module('csp-reporter');
        $module->set(CspPersistable::class, $persisterMock);
        $module->set(CspLimiter::class, $limiterMock);

        $controller = new CraftController(2, $module, []);
        $controller->actionIndex();
    }
}
