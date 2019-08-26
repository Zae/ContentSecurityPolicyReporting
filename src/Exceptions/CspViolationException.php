<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Exceptions;

use Exception;

/**
 * Class CspViolationException
 *
 * @package Zae\ContentSecurityPolicyReporting\Exceptions
 */
class CspViolationException extends Exception
{
    public $blockedUri = '';
    public $disposition = '';
    public $documentUri = '';
    public $effectiveDirective = '';
    public $originalPolicy = '';
    public $referrer = '';
    public $scriptSample = '';
    public $statusCode = '';
    public $violatedDirective = '';

    /**
     * @param string $blockedUri
     * @param string $disposition
     * @param string $documentUri
     * @param string $effectiveDirective
     * @param string $originalPolicy
     * @param string $referrer
     * @param string $scriptSample
     * @param string $statusCode
     * @param string $violatedDirective
     */
    public function setViolation(
        $blockedUri = '',
        $disposition = '',
        $documentUri = '',
        $effectiveDirective = '',
        $originalPolicy = '',
        $referrer = '',
        $scriptSample = '',
        $statusCode = '',
        $violatedDirective = ''
    ): void {
        $this->blockedUri           = $blockedUri;
        $this->disposition          = $disposition;
        $this->documentUri          = $documentUri;
        $this->effectiveDirective   = $effectiveDirective;
        $this->originalPolicy       = $originalPolicy;
        $this->referrer             = $referrer;
        $this->scriptSample         = $scriptSample;
        $this->statusCode           = $statusCode;
        $this->violatedDirective    = $violatedDirective;
    }
}
