<?php

    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Footer;

?>

<!-- =====================
          UPDATE FORM
     ===================== -->

<div class="Block">

    <?php
        echo Form::model(
            $item,
            [
                'route' => [$blueprint->getRouteName('putUpdate'), $item->getKey()],
                'method' => 'PUT',
                'class' => 'Form--sendAjax Form--warnBeforeExit Form--submitOnKeydown'
            ]
        );

        foreach($blueprint->getFields() as $field) {
            $field = EditableField::fromModel($field, $item);
            echo $field->render();
        }

        if(isset($extraFields)) {
            foreach($extraFields as $field) {
                echo $field->render();
            }
        }

        if(!isset($footer)) {
            $footer = new Footer([
                [
                    'route' => $item->softDeletes() && $item->trashed() ? $blueprint->getRouteName('getTrash') : $blueprint->getRouteName('getList'),
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

