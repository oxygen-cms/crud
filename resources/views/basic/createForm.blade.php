<?php

    use Oxygen\Core\Html\Form\EditableField;
    use Oxygen\Core\Html\Form\Footer;use Oxygen\Core\Html\Form\Label;use Oxygen\Core\Html\Form\Row;use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

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
            if(!$field->editable) {
                continue;
            }
            $field = new EditableField($field);
            $label = new Label($field->getMeta());
            $row = new Row([$label, $field]);
            echo $row->render();
        endforeach;

        $footer = new Row([
            new ButtonToolbarItem(Lang::get('oxygen/crud::ui.close'), method_exists($item, 'isDeleted') && $item->isDeleted() ? $blueprint->getAction('getTrash') : $blueprint->getAction('getList')),
            new SubmitToolbarItem(Lang::get('oxygen/crud::ui.create'))
        ]);
        $footer->isFooter = true;

        echo $footer->render();

        echo Form::close();
    ?>

</div>