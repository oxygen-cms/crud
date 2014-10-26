<?php

    use Oxygen\Core\Html\Header\Header;

    $sectionHeader = Header::fromBlueprint(
        $blueprint,
        $title
    );

    $sectionHeader->setBackLink(URL::route(
        $item->trashed()
            ? $blueprint->getRouteName('getTrash')
            : $blueprint->getRouteName('getList')
    ));

    $itemHeader = Header::fromBlueprint($blueprint, null, ['model' => $item], Header::TYPE_NORMAL, 'item');

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {{ $sectionHeader->render() }}
    {{ $itemHeader->render() }}
</div>