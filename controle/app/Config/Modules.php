<?php

namespace Config;

use CodeIgniter\Modules\Modules as BaseModules;

/**
 * Modules Configuration.
 */
class Modules extends BaseModules
{
    /**
     * Enable Auto-Discovery?
     */
    public $enabled = true;

    /**
     * Enable Auto-Discovery Within Composer Packages?
     */
    public $discoverInComposer = true;

    /**
     * The Composer package list for Auto-Discovery
     */
    public $composerPackages = [];

    /**
     * Auto-Discovery Rules
     */
    public $aliases = [
        'events',
        'filters',
        'registrars',
        'routes',
        'services',
    ];
}
