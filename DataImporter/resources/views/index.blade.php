@extends('layout')

@section('content')

    <form action="{{ route('data_importer.showdata') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="flexy mb-24">
            <h1>Data Importer</h1>
        </div>

        <div class="card">
            <div class="flexy">
                <div class="fill">
                    <p>Select your file to import and set CSV delmiter. Defaults to ","</p>
                </div>
                <button type="submit" class="btn btn-primary btn-lg ml-16">Continue</button>
            </div>
        </div>
        <div class="card">
            <div class="form-group fill">
                <div class="field-inner">
                    <label class="block">CSV File</label>
                    <input type="file" class="form-control" name="file" />
                </div>
            </div>
            <div class="form-group fill ">
                <div class="inner">
                    <label class="block">CSV Delimiter</label>
                    <small class="help-block">
                        <p>Defaults to ","</p>
                    </small>
                    <input type="text" class="form-control" name="csv_delimiter" value=""/>
                </div>
            </div>
        </div>
    </form>


@endsection
