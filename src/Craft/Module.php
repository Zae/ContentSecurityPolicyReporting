<?php
declare(strict_types=1);

namespace Zae\ContentSecurityPolicyReporting\Craft;

use Craft;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * Class Module
 *
 * @package Zae\ContentSecurityPolicyReporting\Craft
 */
class Module extends \yii\base\Module
{
    public static $plugin;

    public function init(): void
    {
        Craft::setAlias('@Zae/ContentSecurityPolicyReporting', $this->getBasePath());

        // Set the controllerNamespace based on whether this is a console or web request
        if (!Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'Zae\\ContentSecurityPolicyReporting\\Http\\Controllers';
        }

        parent::init();

        $this->registerEvents();

        static::$plugin = $this;
    }

    private function registerEvents(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = [
                    'csp-report' =>  sprintf('%s/craft', $this->getUniqueId())
                ] + $event->rules;
            }
        );
    }
}
