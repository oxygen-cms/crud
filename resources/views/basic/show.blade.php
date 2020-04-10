@extends(app('oxygen.layout'))

@section('content')

<?php

use Oxygen\Core\Html\Form\Label;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Form\StaticField;

$title = Lang::get('oxygen/crud::ui.namedResource.show', [
        'name' => $item->getAttribute($crudFields->getTitleFieldName())
]);

$sectionTitle = Lang::get('oxygen/crud::ui.resource.show', [
        'resource' => $blueprint->getDisplayName()
]);

?>

<div class="Block">

    @include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'title' => $sectionTitle])

    <?php
        foreach($crudFields->getFields() as $field) {
            $field = StaticField::fromEntity($field, $item);
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            echo $row->render();
        }
    ?>

</div>

@stop
