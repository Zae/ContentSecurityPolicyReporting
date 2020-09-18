<?php

declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Tests\Traits;

use craft\web\Request;
use craft\web\Response;
use Mockery;
use yii\di\Container;
use Zae\ContentSecurityPolicyReporting\Tests\CraftStub;

/**
 * Trait YiiBaseMock
 *
 * @package Zae\ContentSecurityPolicyReporting\Tests\Traits
 */
trait YiiBaseMock
{
    /**
     * Set up all necessary base mocks in Yii and Craft to get a mocked request and
     * response object.
     *
     * @return array<Request, Response>|array
     */
    public function getYiiBaseMocks(): array
    {
        $craftMock = Mockery::mock('overload:Craft', CraftStub::class);
        $yiiMock = Mockery::mock('overload:Yii', CraftStub::class);
        $appMock = Mockery::mock();

        $containerMock = Mockery::mock(Container::class)->makePartial();
        $requestMock = Mockery::mock(Request::class);
        $responseMock = Mockery::mock(Response::class);

        // newer versions use the DI system to get request / response objects,
        // old versiond don't.
        $containerMock->shouldReceive('get')
            ->with('request')
            ->zeroOrMoreTimes()
            ->andReturn($requestMock);

        // newer versions use the DI system to get request / response objects,
        // old versiond don't.
        $containerMock->shouldReceive('get')
                      ->with('response')
                      ->zeroOrMoreTimes()
                      ->andReturn($responseMock);

        $appMock->shouldReceive('has')
                ->zeroOrMoreTimes()
                ->andReturn(false);

        $appMock->request = $requestMock;
        $craftMock::$app = $appMock;
        $yiiMock::$app = $appMock;
        $yiiMock::$container = $containerMock;

        // old versions of yii / craft get the response this way, newer versions use
        // the DI system.
        $appMock->shouldReceive('getResponse')
            ->zeroOrMoreTimes()
            ->andReturn($responseMock);

        return [
            $requestMock,
            $responseMock
        ];
    }
}
