<?php
declare(strict_types=1);

namespace remita\craftremitapayment;

use craft\base\Plugin as BasePlugin;
use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;
use remita\craftremitapayment\gateways\RemitaGateway;

class Plugin extends BasePlugin
{
    public bool $hasCpSettings = false;
    
    public function init(): void
    {
        parent::init();

        Event::on(
            Gateways::class,
            Gateways::EVENT_REGISTER_GATEWAY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = RemitaGateway::class;
            }
        );
    }
}
