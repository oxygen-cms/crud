<?php

use Oxygen\Core\Html\Form\EditableField;
use Oxygen\Core\Html\Form\Form;
use Oxygen\Core\Html\Form\Label;
use Oxygen\Core\Html\Form\Row;
use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

$form = new Form($blueprint->getAction('postCreate'));
$form->setAsynchronous(true)->setSubmitOnShortcutKey(true)->setWarnBeforeExit(true);

foreach($fields->getFields() as $field):
    if(!$field->editable) {
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
endforeach;

if(isset($extraFields)) {
    foreach($extraFields as $field) {
        $form->addContent($field);
    }
}

$footer = new Row([
    new SubmitToolbarItem(__('oxygen/crud::ui.create'))
]);
$footer->isFooter = true;
$form->addContent($footer);

echo $form->render();
