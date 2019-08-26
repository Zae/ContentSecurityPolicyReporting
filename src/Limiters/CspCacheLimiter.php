<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Limiters;

use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Illuminate\Cache\RateLimiter;

/**
 * Class CspCacheLimiter
 *
 * @package Zae\ContentSecurityPolicyReporting\Limiters
 */
class CspCacheLimiter implements CspLimiter
{
    /**
     * @var RateLimiter
     */
    private $limiter;

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $maxAttempts;

    /**
     * @var int
     */
    private $decay;

    /**
     * CspCacheLimiter constructor.
     *
     * @param RateLimiter $limiter
     * @param string      $key
     * @param int         $maxAttempts
     * @param int         $decay
     */
    public function __construct(RateLimiter $limiter, $key = 'csp-rate-limiter', $maxAttempts = 100, $decay = 60)
    {
        $this->limiter = $limiter;
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decay = $decay;
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @return bool
     */
    public function tooManyAttempts(): bool
    {
        return $this->limiter->tooManyAttempts($this->key, $this->maxAttempts);
    }

    /**
     * Increment the counter for the given decay time.
     */
    public function hit(): void
    {
        $this->limiter->hit($this->key, $this->decay);
    }
}
