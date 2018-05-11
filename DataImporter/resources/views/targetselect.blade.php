@extends('layout')

@section('content')
    <form action="{{ route('data_importer.import') }}" method="post">
        {{ csrf_field() }}

        <div class="card flush flat-bottom">
            <div class="head">
                <h1>Select Target</h1>

                <div class="controls">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <input type="hidden"
                   name="selectedcollection"
                   v-model="$refs.handle.data">
            <collections-fieldtype
                    :config="{
                    type: 'collections',
                    max_items: 1,
                    required: true
                }"
                    name="handle"
                    v-ref:handle
            ></collections-fieldtype>
        </div>
    </form>
@endsection
