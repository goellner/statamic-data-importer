<?php

namespace Statamic\Addons\DataImporter;

use Statamic\Extend\Fieldtype;
use Illuminate\Http\Request;

class DataImporterFieldtype extends Fieldtype
{
    /**
     * The blank/default value
     *
     * @return array
     */
    public function blank()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        unset($data['uploaded_data_keys']);
        unset($data['selected_collection_fields']);
        return $data;
    }
}
