@extends(app('oxygen.layout'))

@section('content')

<?php
    $title = Lang::get('oxygen/crud::ui.namedResource.update', [
        'name' => $item->getAttribute($crudFields->getTitleFieldName())
    ]);

    $sectionTitle = Lang::get('oxygen/crud::ui.resource.update', [
        'resource' => $blueprint->getDisplayName()
    ]);
?>

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'title' => $sectionTitle])

@include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item])

@stop
