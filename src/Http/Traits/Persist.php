<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Http\Traits;

use Illuminate\Support\Arr;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;

/**
 * Trait CspReportController
 *
 * @package Zae\ContentSecurityPolicyReporting\Http\Traits
 */
trait Persist
{
    /**
     * @param CspPersistable $persistable
     * @param CspLimiter     $limiter
     * @param array          $cspReport
     */
    public function persist(
        CspPersistable $persistable,
        CspLimiter $limiter,
        array $cspReport = []
    ): void
    {
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
    }
}
