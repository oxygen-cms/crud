@extends(app('oxygen.layout'))

@section('content')

<?php
    $title = __('oxygen/crud::ui.namedResource.update', ['name' => $item->getTitle()]);

    $sectionTitle = __('oxygen/crud::ui.resource.update', [
            'resource' => $blueprint->getDisplayName()
    ]);
?>

<div class="Block">

    @include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'title' => $sectionTitle])

    <?php

    use Oxygen\Core\Form\FieldMetadata;
    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Data\Behaviour\Publishes;

    $versionFieldMeta = new FieldMetadata('version', 'select', true);
    $versionFieldMeta->options = [
            'new' => 'Save as New Version',
            'overwrite' => 'Overwrite Existing Version',
            'guess' => 'Create a New Version if Needed'
    ];
    $versionField = new EditableField($versionFieldMeta, 'guess');
    $versionRow = new Row([new Label($versionField->getMeta()), $versionField]);

    ?>

    @include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'fields' => $crudFields, 'item' => $item, 'extraFields' => [$versionRow]])

</div>

@include('oxygen/crud::versionable.versions', ['item' => $item, 'fields' => $crudFields])

@stop
