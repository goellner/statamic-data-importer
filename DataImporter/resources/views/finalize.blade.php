@extends('layout')

@section('content')

        <div class="flexy mb-24">
            <h1>Import done</h1>

        </div>

        <div class="flexy mb-24">
            <p>From <strong>{{ $uploaded_row_count }}</strong> uploaded rows of data <strong>{{ $imported_row_count }}</strong> rows have been imported.</p>
        </div>

        @if (($failed_cnt) !== 0)
            <div class="flexy mb-24">
                <p>
                    The following rows have failed:
                </p>
            </div>
            <div class="flexy mb-24">
                <ul>
                    @foreach ($failed_entries as $entry)
                        <li>{{ implode (", ", $entry) }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="flexy mb-24">
                <p>
                    Most likely there are duplicate titles in your data.
                </p>
            </div>
        @endif


@endsection
