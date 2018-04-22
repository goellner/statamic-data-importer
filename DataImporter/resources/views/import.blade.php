@extends('layout')

@section('content')
    <form action="{{ route('data_importer.finalize') }}" method="POST">
        {{ csrf_field() }}
        <div class="card flush">
            <div class="head">
                <h1>Map your data</h1>


                <div class="controls">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="form-group">
                        <div class="field-inner">
                            <label class="block">Array Delmiter</label>
                            <small class="help-block">
                                <p>Set delimiter if one of your fields contains multiple entries. Defaults to pipe character "|"</p>
                            </small>
                            <input type="text" name="array_delimiter">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h2>Fieldset Data</h2>
                    <data_importer-fieldtype :config="{ uploaded_data_keys: {{ $uploaded_data_keys_json }}, fieldset_content: {{ $fieldset_content_json }} }" name="mapping" id="mapping"
                    ></data_importer-fieldtype>

                </div>
            </div>
            <div class="col-md-6">
                    @for ($i = 0; $i < 1; $i++)
                        <div class="card">
                            <h2>Data for import</h2>
                            <table class="dossier mt-0">
                                @foreach ($uploaded_data[$i] as $key => $value)
                                    <tr>
                                        <th width="25%">{{ $key }}</th>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endfor
            </div>

        </div>
    </form>
@endsection
