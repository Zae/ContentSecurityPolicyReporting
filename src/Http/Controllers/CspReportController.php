<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Http\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Illuminate\Support\Arr;

/**
 * Class CspReportController
 *
 * @package Zae\ContentSecurityPolicyReporting\Http\Controllers
 */
class CspReportController
{
    /**
     * @param ServerRequestInterface   $request
     * @param CspPersistable           $persistable
     * @param CspLimiter               $limiter
     * @param ResponseFactoryInterface $responseFactory
     *
     * @return ResponseInterface
     */
    public function report(
        ServerRequestInterface $request,
        CspPersistable $persistable,
        CspLimiter $limiter,
        ResponseFactoryInterface $responseFactory
    ): ResponseInterface
    {
        $cspReport = json_decode((string)$request->getBody(), true);
        $cspArray = Arr::get($cspReport, 'csp-report', []);

        if (!$limiter->tooManyAttempts()) {
            $persistable->persist(Arr::only($cspArray, [
                'blocked-uri',
                'disposition',
                'document-uri',
                'effective-directive',
                'original-policy',
                'referrer',
                'script-sample',
                'status-code',
                'violated-directive'
            ]));

            $limiter->hit();
        }

        return $responseFactory->createResponse(202);
    }
}
