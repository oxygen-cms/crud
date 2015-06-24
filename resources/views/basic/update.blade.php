@extends(app('oxygen.layout'))

@section('content')

<?php

    $title = Lang::get('oxygen/crud::ui.resource.update', [
        'resource' => $blueprint->getDisplayName()
    ]);

?>

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => $title])

@include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item])

@stop
