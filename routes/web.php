<?php
declare(strict_types=1);

Route::post('csp-report', [
    'uses' => 'Zae\ContentSecurityPolicyReporting\Http\Controllers\CspReportController@report',
    'as' => 'csp.report'
]);
