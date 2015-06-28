<?php
    use Oxygen\Core\Html\Dialog\Dialog;
    use Oxygen\Core\Http\Method;

    $dialog = new Dialog(Lang::get('oxygen/crud::dialogs.publishable.makeDraft'));
    $buttonAttributes = array_merge(
        ['type' => 'submit'],
        $dialog->render()
    );
    $formAttributes = [
        'class' => 'Form--sendAjax Form--autoSubmit',
        'method' => Method::POST,
        'style'  => 'display: none;',
        'action' => URL::route($blueprint->getRouteName('postMakeDraft'), $item->getId())
    ];
?>

<form {!! html_attributes($formAttributes) !!}>
    {{ Form::token(); }}
    <button {!! html_attributes($buttonAttributes) !!}>Submit</button>
</form>