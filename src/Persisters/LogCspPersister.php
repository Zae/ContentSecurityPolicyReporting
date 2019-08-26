<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Persisters;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;

/**
 * Class LogCspPersister
 *
 * @package Zae\ContentSecurityPolicyReporting\Persisters
 */
class LogCspPersister implements CspPersistable
{
    /**
     * @var string
     */
    private $loglevel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogCspPersister constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $loglevel
     */
    public function __construct(LoggerInterface $logger, string $loglevel = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->loglevel = $loglevel;
    }

    /**
     * @param array $report
     */
    public function persist(array $report): void
    {
        $stringReport = collect($report)->map(static function ($value, $key): string {
            return "{$key}: {$value}";
        })->join('; ');

        $this->logger->{$this->loglevel}("CSP-Report: {$stringReport}");
    }
}
