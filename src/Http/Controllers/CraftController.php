<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Http\Controllers;

use Craft;
use craft\web\Controller;
use Zae\ContentSecurityPolicyReporting\Contracts\CspLimiter;
use Zae\ContentSecurityPolicyReporting\Contracts\CspPersistable;
use Zae\ContentSecurityPolicyReporting\Http\Traits\Persist;

use function json_decode;

/**
 * Class YiiController
 *
 * @package Zae\ContentSecurityPolicyReporting\Http\Controllers
 */
class CraftController extends Controller
{
    use Persist;

    protected $allowAnonymous = ['index'];

    /**
     * I don't know how Yii DI method injection works,
     * so just fetch the classes ourselves and call the
     * action haha.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): void
    {
        /** @var CspPersistable $persistable */
        $persistable = $this->module->get(CspPersistable::class);

        /** @var CspLimiter $limiter */
        $limiter = $this->module->get(CspLimiter::class);

        $this->actionPersist($persistable, $limiter);
    }

    /**
     * @param CspPersistable $persistable
     * @param CspLimiter     $limiter
     */
    private function actionPersist(
        CspPersistable $persistable,
        CspLimiter $limiter
    ): void
    {
        $cspReport = json_decode(
            (string)Craft::$app->request->getRawBody(),
            true
        );

        $this->persist($persistable, $limiter, $cspReport);

        $this->asJson([])->setStatusCode(202);
    }
}
