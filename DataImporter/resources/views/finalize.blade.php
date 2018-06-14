@extends('layout')

@section('content')

        <div class="flexy mb-24">
            <h1>Import done</h1>

        </div>

        <div class="flexy mb-24">
            <p>From <strong>{{ $uploaded_row_count }}</strong> uploaded rows of data <strong>{{ $imported_row_count }}</strong> rows have been imported.</p>
        </div>


@endsection
