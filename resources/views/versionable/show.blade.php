@extends(app('oxygen.layout'))

@section('content')

@include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => 'Show ' . $blueprint->getDisplayName()])

<?php
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\StaticField;
    use Oxygen\Core\Html\Form\Row;
?>

<!-- =====================
             INFO
     ===================== -->

<div class="Block">
    <?php
    foreach($blueprint->getFields() as $field):
        $field = StaticField::fromEntity($field, $item);
        $label = new Label($field->getMeta());
        $row = new Row([$label, $field]);
        echo $row->render();
    endforeach;
    ?>
</div>

@include('oxygen/crud::versionable.versions', ['item' => $item])

@stop
