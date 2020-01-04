<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Persisters;

use Bugsnag\Client;
use Bugsnag\Report;
use Illuminate\Support\Arr;
use Psr\Log\LogLevel;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Zae\ContentSecurityPolicyReporting\Exceptions\CspViolationException;

/**
 * Class BugsnagCspPersister
 *
 * @package Zae\ContentSecurityPolicyReporting\Persisters
 */
class BugsnagCspPersister implements CspPersistable
{
    /**
     * @var string
     */
    private $loglevel;

    /**
     * @var Client
     */
    private $client;

    /**
     * BugsnagCspPersister constructor.
     *
     * @param Client $client
     * @param string $loglevel
     */
    public function __construct(Client $client, string $loglevel = LogLevel::INFO)
    {
        $this->client = $client;
        $this->loglevel = $loglevel;
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

        $this->client->notifyException($cspViolationException, function (Report $bugsnagReport) use ($report) {
            $bugsnagReport->addMetaData($report);
            $bugsnagReport->setSeverity($this->loglevel);
        });
    }
}
