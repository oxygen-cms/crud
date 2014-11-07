@extends(Config::get('oxygen/core::layout'))

@section('content')

<?php

    use Oxygen\Core\Html\Form\StaticField;

    $title = Lang::get('oxygen/crud::ui.resource.show', [
        'resource' => $blueprint->getDisplayName()
    ]);

?>

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'item' => $item, 'title' => $title])

<!-- =====================
             INFO
     ===================== -->

<div class="Block">
    <?php
        foreach($blueprint->getFields() as $field):
            $field = StaticField::fromEntity($field, $item);
            echo $field->render();
        endforeach;
    ?>
</div>

@stop
