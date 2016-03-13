@extends(app('oxygen.layout'))

@section('content')

@include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'fields' => $fields, 'item' => $item, 'title' => $title])

<!-- =====================
             INFO
     ===================== -->

<div class="Block">
    <?php
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\StaticField;
    use Oxygen\Core\Html\Form\Row;
    foreach($fields->getFields() as $field):
        $field = StaticField::fromEntity($field, $item);
        $label = new Label($field->getMeta());
        $row = new Row([$label, $field]);
        echo $row->render();
    endforeach;
    ?>
</div>

@include('oxygen/crud::versionable.versions', ['item' => $item])

@stop
