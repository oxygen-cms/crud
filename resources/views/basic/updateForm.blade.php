<?php

    use Oxygen\Core\Html\Form\EditableField;
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
        echo Form::model(
            $item,
            [
                'route' => [$blueprint->getRouteName('putUpdate'), $item->getId()],
                'method' => 'PUT',
                'class' => 'Form--sendAjax Form--warnBeforeExit Form--submitOnKeydown'
            ]
        );

        foreach($blueprint->getFields() as $field) {
            if(!$field->editable) {
                continue;
            }
            $field = EditableField::fromEntity($field, $item);
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            echo $row->render();
        }

        if(isset($extraFields)) {
            foreach($extraFields as $field) {
                echo $field->render();
            }
        }

        if(!isset($footer)) {
            $footer = new Row([
                new ButtonToolbarItem(Lang::get('oxygen/crud::ui.close'), method_exists($item, 'isDeleted') && $item->isDeleted() ? $blueprint->getAction('getTrash') : $blueprint->getAction('getList')),
                new SubmitToolbarItem(Lang::get('oxygen/crud::ui.save'))
            ]);
            $footer->isFooter = true;
        }

        echo $footer->render();

        echo Form::close();
    ?>

</div>

