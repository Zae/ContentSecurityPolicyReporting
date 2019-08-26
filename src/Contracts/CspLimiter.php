<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Contracts;

/**
 * Interface CspLimiter
 *
 * @package Zae\ContentSecurityPolicyReporting\Contracts
 */
interface CspLimiter
{
    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @return bool
     */
    public function tooManyAttempts(): bool;

    /**
     * Increment the counter the given decay time.
     */
    public function hit(): void;
}
