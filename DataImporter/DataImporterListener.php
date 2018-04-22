<?php

namespace Statamic\Addons\DataImporter;

use Statamic\API\Nav;
use Statamic\Extend\Listener;

class DataImporterListener extends Listener
{
    public $events = [
        'cp.nav.created' => 'addNavItems'
    ];

    public function addNavItems($nav)
    {
        $data_importer = Nav::item('Import Data')->route('addons.data_importer')->icon('publish');
        $nav->addTo('tools', $data_importer);
    }
}