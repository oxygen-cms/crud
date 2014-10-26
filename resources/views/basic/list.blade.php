@extends(Config::get('oxygen/core::layout'))

@section('content')

<?php

    use Oxygen\Core\Blueprint\Blueprint;
    use Oxygen\Core\Html\Header\Header;
    use Oxygen\Core\Html\Form\Field;

    $title = Lang::get(
        $isTrash ? 'oxygen/crud::ui.resource.listTrash' : 'oxygen/crud::ui.resource.list',
        ['resources' => $blueprint->getDisplayName(Blueprint::PLURAL)]
    );

    $sectionHeader = Header::fromBlueprint(
        $blueprint,
        $title
    );

    if($isTrash) {
        $sectionHeader->setBackLink(URL::route($blueprint->getRouteName('getList')));
    }

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {{ $sectionHeader->render() }}
</div>

<!-- =====================
             LIST
     ===================== -->

<div class="Block">

    <?php if($items->isEmpty()): ?>
        <h2 class="heading-gamma margin-large">
            @lang('oxygen/crud::ui.noItems')
        </h2>
    <?php endif; ?>

    <?php
        foreach($items->reverse() as $item):
            $itemHeader = Header::fromBlueprint($blueprint, null, ['model' => $item], Header::TYPE_NORMAL, 'item');
            echo $itemHeader->render();
        endforeach;
    ?>

    <?php
        echo $items->links();
    ?>

</div>

@stop
