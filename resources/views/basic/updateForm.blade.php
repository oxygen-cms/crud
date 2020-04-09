<?php

use Oxygen\Core\Html\Form\EditableField;
use Oxygen\Core\Html\Form\Form;
use Oxygen\Core\Html\Form\Label;
use Oxygen\Core\Html\Form\Row;
use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

?>

        <!-- =====================
          UPDATE FORM
     ===================== -->

<div class="Block">

    <?php
    $form = new Form($blueprint->getAction('putUpdate'));
    $form->setAsynchronous(true)->setWarnBeforeExit(true)->setSubmitOnShortcutKey(true);
    $form->setRouteParameterArguments(['model' => $item]);

    foreach($fields->getFields() as $field) {
        if(!$field->editable) {
            continue;
        }

        if(isset($field->options['fullWidth']) && $field->options['fullWidth'] == true) {
            $editableField = EditableField::fromEntity($field, $item);
            $row = new Row([$editableField]);
            $row->addClass('Row--fullWidth');
        } else {
            $editableField = EditableField::fromEntity($field, $item);
            $label = new Label($editableField->getMeta());
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
    ?>

</div>

