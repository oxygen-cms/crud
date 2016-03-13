@extends(app('oxygen.layout'))

@section('content')

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => $title])

@include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item])

@stop
