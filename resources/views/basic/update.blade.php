@extends(app('oxygen.layout'))

@section('content')

<?php
    $title = __('oxygen/crud::ui.namedResource.update', [
            'name' => $item->getAttribute($crudFields->getTitleFieldName())
    ]);
    
    $sectionTitle = __('oxygen/crud::ui.resource.update', [
            'resource' => $blueprint->getDisplayName()
    ]);
?>

<div class="Block">

    @include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'title' => $sectionTitle])

    @include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item])

</div>
    
@stop
