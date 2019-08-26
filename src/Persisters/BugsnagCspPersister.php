<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Persisters;

use Bugsnag;
use Bugsnag\Report;
use Illuminate\Support\Arr;
use Zae\ContentSecurityPolicyReporting\Contracts;
use Zae\ContentSecurityPolicyReporting\Exceptions\CspViolationException;

/**
 * Class BugsnagCspPersister
 *
 * @package Zae\ContentSecurityPolicyReporting\Persisters
 */
class BugsnagCspPersister implements Contracts\CspPersistable
{
    /**
     * @var string
     */
    private $severity;

    /**
     * BugsnagCspPersister constructor.
     *
     * @param string $severity
     */
    public function __construct(string $severity = 'warning')
    {
        $this->severity = $severity;
    }

    /**
     * @param array $report
     */
    public function persist(array $report): void
    {
        $cspViolationException = new CspViolationException('CSP-Report');
        $cspViolationException->setViolation(
            Arr::get($report, 'blockedUri'),
            Arr::get($report, 'disposition'),
            Arr::get($report, 'documentUri'),
            Arr::get($report, 'effectiveDirective'),
            Arr::get($report, 'originalPolicy'),
            Arr::get($report, 'referrer'),
            Arr::get($report, 'scriptSample'),
            Arr::get($report, 'statusCode'),
            Arr::get($report, 'violatedDirective')
        );

        Bugsnag::notifyException($cspViolationException, function (Report $bugsnagReport) use ($report) {
            $bugsnagReport->addMetaData($report);
            $bugsnagReport->setSeverity($this->severity);
        });
    }
}
