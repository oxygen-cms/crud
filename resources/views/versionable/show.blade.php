@extends(Config::get('oxygen/core::layout'))

@section('content')

@include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'item' => $item, 'title' => 'Show ' . $blueprint->getDisplayName()])

<?php
    use Oxygen\Core\Html\Form\StaticField;
?>

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

@include('oxygen/crud::versionable.versions', ['item' => $item])

@stop
