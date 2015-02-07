<?php

    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Footer;use Oxygen\Core\Html\Form\Label;use Oxygen\Core\Html\Form\Row;

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
            $footer = new Footer([
                [
                    'route' => method_exists($item, 'isDeleted') && $item->isDeleted() ? $blueprint->getRouteName('getTrash') : $blueprint->getRouteName('getList'),
                    'label' => Lang::get('oxygen/crud::ui.close')
                ],
                [
                    'type' => 'submit',
                    'label' => Lang::get('oxygen/crud::ui.save')
                ]
            ]);
        }

        echo $footer->render();

        echo Form::close();
    ?>

</div>

