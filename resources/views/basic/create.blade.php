@extends(app('oxygen.layout'))

@section('content')

<?php

use Oxygen\Core\Html\Header\Header;

$title = Lang::get('oxygen/crud::ui.resource.create', [
        'resource' => $blueprint->getDisplayName()
]);

$header = Header::fromBlueprint(
        $blueprint,
        $title
);

$header->setBackLink(URL::route($blueprint->getRouteName('getList')));

?>

        <!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {!! $header->render() !!}

    @include('oxygen/crud::basic.createForm', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item])

</div>

@stop
