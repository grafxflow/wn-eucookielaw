<?php

namespace Grafxflow\Eucookielaw;

use Block;
use Grafxflow\Eucookielaw\Models\Settings;
use Illuminate\Support\Facades\Event;
use URL;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

class Plugin extends PluginBase
{
    public const COOKIEVAL_DENY = 'deny';

    public const COOKIEVAL_ALLOW = 'allow';

    private $pathToPlugin;

    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);
        $this->pathToPlugin = '/plugins/grafxflow/eucookielaw';
    }

    public function boot(): void
    {
        /** @var \Cms\Classes\Controller $controller  */
        Event::listen('cms.page.beforeDisplay', function ($controller, $action, $params): void {
            $controller->addJs($this->pathToPlugin . '/assets/js/cookieconsent.min.js');
            $controller->addCss($this->pathToPlugin . '/assets/css/cookieconsent.min.css');
            $this->initJs();

            $cookieName = Settings::get('cookie_name');
            $controller->vars["cookieconsent_status"] = empty($_COOKIE[ $cookieName ]) ? "" : $_COOKIE[ $cookieName ];
        });
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'allow_cookies' => [$this, 'allowServingCookies'],
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'grafxflow.eucookielaw::lang.settings.label',
                'description' => 'grafxflow.eucookielaw::lang.settings.description',
                'icon' => 'icon-pencil-square-o',
                'class' => 'grafxflow\eucookielaw\models\settings',
                'category' => SettingsManager::CATEGORY_MISC,
                'permissions' => ['grafxflow.eucookielaw.access_settings'],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'grafxflow.eucookielaw.access_settings' => [
                'tab' => 'grafxflow.eucookielaw::lang.plugin.name',
                'label' => 'grafxflow.eucookielaw::lang.eucookielaw.manage_eucookielaw',
            ],
        ];
    }

    /**
    * Function that checks if setting cookies is even allowed
    * @return bool
    */
    public function allowServingCookies()
    {
        $complianceType = Settings::get('type');
        $cookieName = Settings::get('cookie_name');
        $cookieVal = empty($_COOKIE[ $cookieName ]) ? "" : $_COOKIE[ $cookieName ];

        switch ($complianceType) {
            case "opt-in":
            return (bool) (self::COOKIEVAL_ALLOW === $cookieVal);
            case "opt-out":
            return ! (self::COOKIEVAL_DENY === $cookieVal);
            default:
            return true;
        }
    }

    private function initJs(): void
    {
        $settings = new Settings();
        $dismissOnScroll = empty($settings::get('dismissOnScroll')) ? false : $settings::get('dismissOnScroll');
        $dismissOnTimeout = empty($settings::get('dismissOnTimeout')) ? false : $settings::get('dismissOnTimeout');
        $static = empty($settings::get('static')) ? "false" : "true";
        $revokable = empty($settings::get('revokable')) ? "false" : "true";
        $revokeBtnTemplate = json_encode($settings::get('revokeBtn'));

        $configData = [
            'header' => $settings::get('header'),
            'message' => $settings::get('message'),
            'dismiss' => $settings::get('dismiss'),
            'allow' => $settings::get('allow'),
            'deny' => $settings::get('deny'),
            'link' => $settings::get('link'),
            'href' => $settings::get('href'),
            'position' => $settings::get('position'),
            'container' => $settings::get('container'),
            'type' => $settings::get('type'),
            'theme' => $settings::get('theme'),
            'revokable' => ($revokable == 'true' || $revokable === true || $revokable == 1),
            'revokeBtn' => $revokeBtnTemplate,
            'static' => ($static == 'true' || $static === true || $static == 1),
            'dismissOnScroll' => $dismissOnScroll,
            'dismissOnTimeout' => $dismissOnTimeout,
            'cookie_path' => $settings::get('cookie_path'),
            'cookie_name' => $settings::get('cookie_name'),
            'cookie_domain' => $settings::get('cookie_domain'),
            'cookie_expiryDays' => $settings::get('cookie_expiryDays'),
        ];

        $inlineConfig = '
        <script>
        window.CookieNoticeConfig = ' . json_encode($configData) . ';
        window.CookieNoticeConfig.palette = {' . $settings::get('palette') . '};
        window.CookieNoticeConfig.elements = {' . $settings::get('elements') . '};
        window.CookieNoticeConfig.compliance = {' . $settings::get('compliance') . '};
        </script>';

        $assetUrl = Url::asset('plugins/grafxflow/eucookielaw/assets/js/cookie-init.js');
        $scriptFile = '<script src="' . $assetUrl . '" defer></script>';

        Block::append('scripts', $inlineConfig . $scriptFile);
    }
}
