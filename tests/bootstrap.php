<?php
declare(strict_types=1);

/**
 * Test bootstrap — stub the Craft CMS / Craft Commerce / Yii2 classes that
 * the plugin's src files reference at the class-level (extends / implements /
 * use statements).  This lets the pure-PHP helper and response classes be
 * loaded and exercised without a running Craft installation.
 *
 * Only the interfaces and base classes actually needed by the helpers under
 * test are declared here.  Add more stubs as the test suite grows.
 */

// ---------------------------------------------------------------------------
// Yii2 stubs
// ---------------------------------------------------------------------------
namespace yii\base {
    if (!class_exists(\yii\base\Component::class, false)) {
        abstract class Component
        {
            public function init(): void {}
        }
    }
}

// ---------------------------------------------------------------------------
// Craft Commerce base stubs
// ---------------------------------------------------------------------------
namespace craft\commerce\base {

    if (!interface_exists(\craft\commerce\base\RequestResponseInterface::class, false)) {
        interface RequestResponseInterface
        {
            public function isSuccessful(): bool;
            public function isProcessing(): bool;
            public function isRedirect(): bool;
            public function getRedirectUrl(): string;
            public function getRedirectMethod(): string;
            public function getRedirectData(): array;
            public function getMessage(): string;
            public function getCode(): string;
            public function getData(): array;
            public function getTransactionReference(): string;
        }
    }

    if (!class_exists(\craft\commerce\base\Gateway::class, false)) {
        abstract class Gateway extends \yii\base\Component
        {
            public string $handle = '';

            public function rules(): array { return []; }

            abstract public static function displayName(): string;
        }
    }
}

// ---------------------------------------------------------------------------
// Craft helper stubs
// ---------------------------------------------------------------------------
namespace Craft {
    if (!function_exists('Craft\t')) {
        function t(string $category, string $message, array $params = []): string
        {
            return $message;
        }
    }
}

// ---------------------------------------------------------------------------
// PSR-4 autoloader for the plugin's src/ directory
// ---------------------------------------------------------------------------
namespace {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'remita\\craftremitapayment\\';
        $baseDir = __DIR__ . '/../src/';

        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}
