<?php

namespace Statamic\Addons\DataImporter;

use Statamic\Extend\Controller;

use Statamic\API\Collection;
use Statamic\API\Fieldset;
use ParseCsv\Csv;
use Statamic\API\Entry;
use Statamic\API\Str;
use Statamic\API\Helper;

class DataImporterController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return mixed
     */

    public function index()
    {
        return $this->view('index');
    }

    public function targetselect()
    {
        return $this->view('targetselect');
    }

    public function showdata()
    {
        if (request('csv_delimiter')) {
            $csv_delimiter = request('csv_delimiter');
        } else {
            $csv_delimiter = ',';
        }
        $uploaded_data = $this->csv_to_array($this->request->file('file'), $csv_delimiter);

        $this->request->session()->put('uploaded_data', $uploaded_data->data);
        $uploaded_data_keys = $uploaded_data->titles;

        $this->request->session()->put('uploaded_data_keys', $uploaded_data_keys);

        $data = [
            'file' => $uploaded_data->data,
            'row_count' => count($uploaded_data->data),
            'preview_count' => $this->previewCount($uploaded_data->data),
        ];
        return $this->view('showdata', $data);
    }

    public function import()
    {
        $handle = request('selectedcollection');
        $collection = Collection::whereHandle($handle);
        $collection_fieldset = $collection->get('fieldset');
        $fieldset = Fieldset::get($collection_fieldset);
        $fieldset_content = $fieldset->fields();

        $this->request->session()->put('selected_collection', $handle);

        $this->request->session()->put('selected_collection_fields', $fieldset_content);

        $data = [
            'title' => 'Data Importer',
            'btn_text' => 'Import',
            'uploaded_data' => $this->request->session()->get('uploaded_data'),
            'uploaded_data_keys' => $this->request->session()->get('uploaded_data_keys'),
            'uploaded_data_keys_json' => json_encode($this->request->session()->get('uploaded_data_keys')),
            'fieldset_content_json' => json_encode($fieldset_content),
        ];

        return $this->view('import', $data);
    }

    private function csv_to_array($file, $csv_delimiter = ',')
    {
        $csv = new Csv();
        $csv->delimiter = $csv_delimiter;
        $csv->parse($file);

        return $csv;
    }

    public function finalize()
    {
        $import_id = Helper::makeUuid();

        $mapping = request('mapping');
        if (request('array_delmiter')) {
            $array_delimiter = request('array_delmiter');
        } else {
            $array_delimiter = '|';
        }

        $entries = $this->request->session()->get('uploaded_data');
        $collection = $this->request->session()->get('selected_collection');
        $failed_entries = $this->save($entries, $collection, $mapping, $array_delimiter, $import_id);

        $data = [
            'uploaded_row_count' => count($entries),
            'imported_row_count' => count($entries) - sizeof($failed_entries),
            'failed_entries' => $failed_entries,
            'failed_cnt' => sizeof($failed_entries)
        ];

        $this->request->session()->remove('uploaded_data');
        $this->request->session()->remove('uploaded_row_count');
        $this->request->session()->remove('selected_collection');
        $this->request->session()->remove('selected_collection_fields');


        return $this->view('finalize', $data);
    }

    private function save($entries, $collection, $mapping, $array_delimiter, $import_id)
    {
        $self = $this;

        $failed = [];

        $mapped_data = collect($entries)->map(function ($entry) use ($mapping) {
            $ret = array();

            foreach ($mapping as $key => $value) {
                if (array_key_exists($value, $entry)) {
                    $ret[$key] = $entry[$value];
                }
            }

            return $ret;
        })->each(function ($entry) use ($collection, $self, $array_delimiter, $import_id, &$failed) {
            $success = $self->writeEntry($collection, $entry, $array_delimiter, $import_id);

            if(!$success) $failed[] = $entry;
        });

        return $failed;
    }

    private function writeEntry($collection, $mapped_data, $array_delimiter, $import_id)
    {
        $entry = Entry::whereSlug(Str::slug($mapped_data['title']), $collection);

        if($entry && $entry->get('import_id') == $import_id) {
            return false;
        }

        if (!$entry) {
            $date = date('Y-m-d-Hi');
            $entry = Entry::create(Str::slug($mapped_data['title']))
                        ->collection($collection)
                        ->get();
        }

        foreach ($mapped_data as $key => $value) {
            if (strpos($value, $array_delimiter)) {
                $value = explode($array_delimiter, $value);
            }
            $entry->set($key, $value);
        }

        $entry->set('import_id', $import_id);

        $ret = $entry->save();

        return true;
    }

    private function previewCount($data) {
        if(sizeOf($data) > 5) {
            return 5;
        } else {
            return sizeOf($data);
        }
    }
}
