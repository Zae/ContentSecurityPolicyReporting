# Content Security Policy Reporting

[![Latest Version](https://img.shields.io/github/release/Zae/ContentSecurityPolicyReporting.svg?style=flat-square)](https://github.com/Zae/strict-transport-security/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/Zae/ContentSecurityPolicyReporting.svg?style=flat-square)](https://packagist.org/packages/Zae/strict-transport-security)

Catch the policy violations your users generate and store them somewhere

## Install

Via Composer

``` bash
$ composer require zae/csp-reporting
```

## Usage

### Laravel
Publish the config file using `artisan vendor:publish`


``` php
#config/csp-report.php

return [
    'persist' => [
        'class' =>  LogCspPersister::class,
        'properties' => [
            'loglevel' => Psr\Log\LogLevel::INFO
        ]
    ],
    'limiter' => [
        'class' => CspCacheLimiter::class,
        'properties' => [
            'key' => 'csp-rate-limiter',
            'maxAttempts' => 1,
            'decay' => 60
        ]
    ],
];
```

By default the plugin will store the violations in the log, but there is also a `BugsnagPersister` that
will send the violation to bugsnag.

On high traffic sites the violations might occur often and probably they will all be the same, so the
limiter will make sure only a part of the violations will actually be stored.

The properties array will allow you to configurate the handlers.

You can also provide your own classes as long as they implement the right interfaces.

## Provided Limiters
- `CspCacheLimiter`
- `CspLotteryLimiter`

## Provided Persisters
- `LogCspPersister`
- `BugsnagCspPersister`


## Testing

__ Not actually working yet tho, MR's are appreciated :D __

``` bash
$ phpunit
```

## Contributing

Contributions are welcome via pull requests on github.

## Credits

- [Ezra Pool](https://github.com/Zae)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
