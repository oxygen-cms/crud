<?php

    use Oxygen\Core\Html\Header\Header;

    if(!function_exists('getSubtitleForItem')) {
        function getSubtitleForItem($item) {
            if($item->isHead()) {
                return '(latest version)';
            } else {
                return 'from ' . $item->updated_at->diffForHumans();
            }
        }
    }

    $sectionHeader = Header::fromBlueprint(
        $blueprint,
        $title
    );

    if(!$item->isHead()) {
        $backLink = URL::route($blueprint->getRouteName('getUpdate'), $item->getHeadKey());
    } else if($item->trashed()) {
        $backLink = URL::route($blueprint->getRouteName('getTrash'));
    } else {
        $backLink = URL::route($blueprint->getRouteName('getList'));
    }
    $sectionHeader->setBackLink($backLink);

    $itemHeader = Header::fromBlueprint($blueprint, null, ['model' => $item], Header::TYPE_NORMAL, 'item');

    if(Auth::user()->hasPermissions($blueprint->getRouteName() . '.versions')) {
        $itemHeader->setSubtitle(getSubtitleForItem($item));
    }

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {{ $sectionHeader->render() }}
    {{ $itemHeader->render() }}
</div>