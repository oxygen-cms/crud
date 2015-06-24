<?php

    use Oxygen\Core\Html\Header\Header;

    $sectionHeader = Header::fromBlueprint(
        $blueprint,
        $title
    );

    $sectionHeader->setBackLink(URL::route(
        $item->isDeleted()
            ? $blueprint->getRouteName('getTrash')
            : $blueprint->getRouteName('getList')
    ));

    $itemHeader = Header::fromBlueprint($blueprint, $fields, ['model' => $item], Header::TYPE_NORMAL, 'item');

    $blockClasses = ['Block'];
    if(isset($seamless) && $seamless == true) {
        $blockClasses[] = 'Block--noBorder';
        $blockClasses[] = 'Block--noMargin';
    }

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="{{{ implode(' ', $blockClasses) }}}">
    {!! $sectionHeader->render() !!}
    {!! $itemHeader->render() !!}
</div>