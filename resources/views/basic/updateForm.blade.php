<?php

use Oxygen\Core\Html\Form\EditableField;
use Oxygen\Core\Html\Form\Form;
use Oxygen\Core\Html\Form\Label;
use Oxygen\Core\Html\Form\Row;
use Oxygen\Core\Html\Form\StaticField;
use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

$form = new Form($blueprint->getAction('putUpdate'));
$form->setAsynchronous(true)->setWarnBeforeExit(true)->setSubmitOnShortcutKey(true);
$form->setRouteParameterArguments(['model' => $item]);

foreach($fields->getFields() as $field) {
    if(!$field->editable) {
        if($field->name === 'id') {
            // TODO: remove special-cased field name
            continue;
        }
        $staticField = StaticField::fromEntity($field, $item);
        $label = new Label($field);
        $row = new Row([$label, $staticField]);
        $form->addContent($row->render());
        continue;
    }

    $editableField = EditableField::fromEntity($field, $item);
    $label = new Label($editableField->getMeta());

    if(isset($field->options['fullWidth']) && $field->options['fullWidth'] == true) {
        $row = new Row([$editableField]);
        $row->addClass('Row--fullWidth');
    } else {
        $row = new Row([$label, $editableField]);
    }
    $form->addContent($row);
}

if(isset($extraFields)) {
    foreach($extraFields as $field) {
        $form->addContent($field);
    }
}

if(!isset($footer)) {
    $footer = new Row([
            new ButtonToolbarItem(Lang::get('oxygen/crud::ui.close'), method_exists($item, 'isDeleted') && $item->isDeleted() ? $blueprint->getAction('getTrash') : $blueprint->getAction('getList')),
            new SubmitToolbarItem(Lang::get('oxygen/crud::ui.save'))
    ]);
    $footer->isFooter = true;
}

$form->addContent($footer);

echo $form->render();

