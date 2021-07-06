@extends('laravel-vue-crud::layouts.app')

@section('content')
    <div class="container">
        <h2>{{$title}}</h2>
        <crud-componentb
                url-ruta="{{$urlRuta}}"
                url-edit="{{$urlEdit}}"
                tabla="{{$tabla}}"
                tablaid="{{$tablaid}}"
                :campos-show="{{ json_encode($camposShow) }}"
                :campos-todos="{{ json_encode($camposTodos) }}"
                :campos-edit="{{ json_encode($camposEdit) }}"
                :left-joins="{{ json_encode($leftJoins) }}"
                :sub-tablas="{{ json_encode($subTablas) }}"
                :botones-extra="{{ json_encode($botonesExtra) }}"
                :botones-encabezado="{{ json_encode($botonesEncabezado) }}"
                usersid="{{ encrypt(auth()->user()->id) }}"
                :button-new="{{ json_encode($buttonNew)  }}"
                :permissions="{{ json_encode($permissions) }}"
                :links="{{ json_encode($links) }}"
                :wheres="{{ json_encode($wheres) }}"
                :wheres-raw="{{ json_encode($wheresRaw) }}"
        >
        </crud-componentb>
    </div>

@endsection
