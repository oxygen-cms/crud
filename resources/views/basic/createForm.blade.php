<?php

    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Footer;

?>

<!-- =====================
         CREATE FORM
     ===================== -->

<div class="Block">

    <?php
        echo Form::model(
            $item,
            array(
                'route' => $blueprint->getRouteName('postCreate'),
                'class' => 'Form--sendAjax Form--warnBeforeExit Form--submitOnKeydown'
            )
        );

        foreach($blueprint->getFields() as $field):
            $field = new EditableField($field);
            echo $field->render();
        endforeach;

        $footer = new Footer([
            [
                'route' => $item->softDeletes() && $item->trashed() ? $blueprint->getRouteName('getTrash') : $blueprint->getRouteName('getList'),
                'label' => Lang::get('oxygen/crud::ui.close')
            ],
            [
                'type' => 'submit',
                'label' => Lang::get('oxygen/crud::ui.create')
            ]
        ]);

        echo $footer->render();

        echo Form::close();
    ?>

</div>