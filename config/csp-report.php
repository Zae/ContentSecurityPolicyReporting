<?php
declare(strict_types=1);

use Zae\ContentSecurityPolicyReporting\Limiters\CspCacheLimiter;
use Zae\ContentSecurityPolicyReporting\Limiters\CspLotteryLimiter;
use Zae\ContentSecurityPolicyReporting\Persisters\LogCspPersister;

return [
    'persist' => [
        'class' => LogCspPersister::class,
        'properties' => [
            'loglevel' => Psr\Log\LogLevel::INFO
        ]
    ],
    'limiter' => [
        'class' => CspCacheLimiter::class,
        'properties' => [
            'key' => 'csp-rate-limiter',
            'maxAttempts' => 2,
            'decay' => 60
        ]
    ],
//    'limiter' => [
//        'class' => CspLotteryLimiter::class,
//        'properties' => [
//            'chance' => 5
//        ]
//    ]
];
