<?php

namespace Statamic\Addons\DataImporter;

use Statamic\Extend\Controller;

use Statamic\API\Collection;
use Statamic\API\Fieldset;
use Illuminate\Http\Request;
use Statamic\API\Entry;
use Statamic\API\Str;

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

    public function targetselect(){

        return $this->view('targetselect');
    }

    public function showdata() {

        if(isset($_POST['csv_delimiter'])) {
            $csv_delimiter = $_POST['csv_delimiter'];
        } else {
            $csv_delimiter = ',';
        }
        $uploaded_data = $this->csv_to_array($this->request->file('file'), $csv_delimiter);
        $this->request->session()->put('uploaded_data', $uploaded_data);

        $uploaded_data_keys = [];
        foreach($uploaded_data as $single_uploaded_data) {
            $uploaded_data_keys = array_keys($single_uploaded_data);
        }

        $this->request->session()->put('uploaded_data_keys', $uploaded_data_keys);

        $data = [
            'file' => $uploaded_data
        ];
        return $this->view('showdata', $data);
    }


    public function import(){
        $handle = $_POST['selectedcollection'];
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

    private function csv_to_array($file, $csv_delimiter = ',') {
        $ret = array_map(function($v) use ($csv_delimiter){return str_getcsv($v, $csv_delimiter);}, file($file));
        array_walk($ret, function(&$a) use ($ret) {
            $a = array_combine($ret[0], $a);
        });
        array_shift($ret);

        return $ret;
    }

    public function finalize() {
        $mapping = $_POST['mapping'];
        if(isset($_POST['array_delmiter'])) {
            $array_delimiter = $_POST['array_delmiter'];
        } else {
            $array_delimiter = '|';
        }

        $entries = $this->request->session()->get('uploaded_data');
        $collection = $this->request->session()->get('selected_collection');
        $this->save($entries, $collection, $mapping, $array_delimiter);
        // TODO: Remove Session data

        return $this->view('finalize');
    }

    private function save($entries,  $collection, $mapping, $array_delimiter) {

        $self = $this;

        $mapped_data = collect($entries)->map(function ($entry) use($mapping){
            $ret = array();

            foreach ($mapping as $key => $value) {
                if(array_key_exists($value, $entry)) {
                    $ret[$key] = $entry[$value];
                }
            }

            return $ret;
        })->each(function ($entry) use($collection, $self, $array_delimiter) {
            $self->writeEntry($collection, $entry, $array_delimiter);
        });
    }

    private function writeEntry($collection, $mapped_data, $array_delimiter){

        $entry = Entry::whereSlug(Str::slug($mapped_data['title']), $collection);

        if(!$entry) {
        $date = date('Y-m-d-Hi');
            $entry = Entry::create(Str::slug($mapped_data['title']))
                        ->collection($collection)
                        ->get();
        }

        foreach($mapped_data as $key => $value) {
            if(strpos($value, $array_delimiter)) {
                $value = explode($array_delimiter, $value);
            }
            $entry->set($key, $value);
        }

        $entry->save();
    }

}