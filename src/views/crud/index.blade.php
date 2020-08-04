@extends('laravel-vue-crud::layouts.app')

@section('content')
    <div class="container">
        <crud-component
                url-ruta="{{$urlRuta}}"
                url-edit="{{$urlEdit}}"
                tabla="{{$tabla}}"
                tablaid="{{$tablaid}}"
                :campos-show="{{ json_encode($camposShow) }}"
                :campos-edit="{{ json_encode($camposEdit) }}"
                :left-joins="{{ json_encode($leftJoins) }}"
                :sub-tablas="{{ json_encode($subTablas) }}"
                :botones-extra="{{ json_encode($botonesExtra) }}"
                usersid="{{ encrypt(auth()->user()->id) }}"
        >
        </crud-component>
    </div>

@endsection