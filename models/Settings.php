<?php

namespace Grafxflow\Wneucookielaw\models;

use Winter\Storm\Database\Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'grafxflow_eucookielaw_settings';

    public $settingsFields = 'fields.yaml';

    protected $cache = [];
}
