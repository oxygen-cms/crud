@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Form\StaticField;

?>

@include('oxygen/crud::basic.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => $title])

<!-- =====================
             INFO
     ===================== -->

<div class="Block">
    <?php
        foreach($fields->getFields() as $field):
            $field = StaticField::fromEntity($field, $item);
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            echo $row->render();
        endforeach;
    ?>
</div>

@stop
