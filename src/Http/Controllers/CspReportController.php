<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Http\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Zae\ContentSecurityPolicyReporting\Http\Traits\Persist;
use function json_decode;

/**
 * Class CspReportController
 *
 * @package Zae\ContentSecurityPolicyReporting\Http\Controllers
 */
class CspReportController
{
    use Persist;

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
        $cspReport = json_decode(
            (string)$request->getBody(),
            true
        );

        $this->persist($persistable, $limiter, $cspReport);

        return $responseFactory->createResponse(202);
    }
}
