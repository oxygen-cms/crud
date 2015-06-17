<?php

use Oxygen\Core\Form\Form;use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
    use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;
    use Oxygen\Core\Html\Toolbar\Toolbar;

?>

<!-- =====================
          UPDATE FORM
     ===================== -->

<div class="Block">

    <?php
        $form = new Form($blueprint->getAction('putUpdate'));
        $form->setAsynchronous(true)->setWarnBeforeExit(true)->setSubmitOnShortcutKey(true);

        foreach($blueprint->getFields() as $field) {
            if(!$field->editable) {
                continue;
            }
            $field = EditableField::fromEntity($field, $item, app('input'));
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
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

