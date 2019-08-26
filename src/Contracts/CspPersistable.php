<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Contracts;

/**
 * Interface CspPersistable
 *
 * @package Zae\ContentSecurityPolicyReporting\Contracts
 */
interface CspPersistable
{
    /**
     * @param array $report
     */
    public function persist(array $report): void;
}
