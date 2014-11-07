<?php
    use Oxygen\Core\Html\Dialog\Dialog;

    $dialog = new Dialog(Lang::get('oxygen/crud::dialogs.publishable.makeDraft'));
    $buttonAttributes = array_merge(
        ['type' => 'submit'],
        $dialog->render()
    );
    $formAttributes = [
        'class' => 'Form--sendAjax Form--autoSubmit',
        'method' => 'POST',
        'style'  => 'display: none;',
        'action' => URL::route($blueprint->getRouteName('postMakeDraft'), $item->getId())
    ];
?>

<form {{ Html::attributes($formAttributes) }}>
    {{ Form::token(); }}
    <button {{ Html::attributes($buttonAttributes) }}>Submit</button>
</form>