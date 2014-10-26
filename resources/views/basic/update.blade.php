@extends(Config::get('oxygen/core::layout'))

@section('content')

<?php

    $title = Lang::get('oxygen/crud::ui.resource.update', [
        'resource' => $blueprint->getDisplayName()
    ]);

?>

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'item' => $item, 'title' => $title])

@include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'item' => $item])

@stop
