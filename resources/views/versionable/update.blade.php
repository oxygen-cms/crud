@extends(Config::get('oxygen/core::layout'))

@section('content')

<?php

    $title = Lang::get('oxygen/crud::ui.resource.update', [
        'resource' => $blueprint->getDisplayName()
    ]);
?>

@include('oxygen/crud::versionable.itemHeader', ['blueprint' => $blueprint, 'item' => $item, 'title' => $title])

<?php

    use Oxygen\Core\Form\Field;
    use Oxygen\Core\Html\Form\EditableField;

    $versionFieldMeta = new Field('version', Field::TYPE_SELECT, true);
    $versionFieldMeta->options = [
        'new' => 'Save as New Version',
        'overwrite' => 'Overwrite Existing Version',
        'guess' => 'Create a New Version if Needed'
    ];
    $versionField = new EditableField($versionFieldMeta, 'guess');

?>

@include('oxygen/crud::basic.updateForm', ['blueprint' => $blueprint, 'item' => $item, 'extraFields' => [$versionField]])

@include('oxygen/crud::versionable.versions', ['item' => $item])

@stop
