<?php

use Oxygen\Core\Html\Form\EditableField;use Oxygen\Core\Html\Form\Footer;use Oxygen\Core\Html\Form\Form;use Oxygen\Core\Html\Form\Label;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

?>

        <!-- =====================
         CREATE FORM
     ===================== -->

<div class="Block">

    <?php
    $form = new Form($blueprint->getAction('postCreate'));
    $form->setAsynchronous(true)->setSubmitOnShortcutKey(true)->setWarnBeforeExit(true);

    foreach($fields->getFields() as $field):
        if(!$field->editable) {
            continue;
        }
        $field = new EditableField($field);
        $label = new Label($field->getMeta());
        $row = new Row([$label, $field]);
        $form->addContent($row);
    endforeach;

    if(isset($extraFields)) {
        foreach($extraFields as $field) {
            $form->addContent($field);
        }
    }

    $footer = new Row([
            new ButtonToolbarItem(__('oxygen/crud::ui.close'), method_exists($item, 'isDeleted') && $item->isDeleted() ? $blueprint->getAction('getTrash') : $blueprint->getAction('getList')),
            new SubmitToolbarItem(__('oxygen/crud::ui.create'))
    ]);
    $footer->isFooter = true;
    $form->addContent($footer);

    echo $form->render();
    ?>

</div>