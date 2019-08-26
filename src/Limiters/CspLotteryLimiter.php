<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Limiters;

use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Exception;

/**
 * Class CspLotteryLimiter
 *
 * @package Zae\ContentSecurityPolicyReporting\Limiters
 */
class CspLotteryLimiter implements CspLimiter
{
    /**
     * @var int
     */
    private $chance;

    /**
     * CspLotteryLimiter constructor.
     *
     * @param int $chance
     */
    public function __construct(int $chance = 10)
    {
        $this->chance = $chance;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function tooManyAttempts(): bool
    {
        return random_int(PHP_INT_MIN, PHP_INT_MAX) % $this->chance === 0;
    }

    public function hit(): void
    {
        // not needed.
    }
}
