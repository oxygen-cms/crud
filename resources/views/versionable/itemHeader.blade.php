<?php

use Oxygen\Core\Html\Header\Header;

if(!function_exists('getSubtitleForItem')) {
    function getSubtitleForItem($version, $item = null) {
        if($version === $item) {
            return Lang::get('oxygen/crud::ui.thisVersion');
        } else if($version->isHead()) {
            return Lang::get('oxygen/crud::ui.latestVersion');
        } else {
            return Lang::get('oxygen/crud::ui.fromDate', [
                    'date' => $version->getUpdatedAt()->diffForHumans()
            ]);
        }
    }
}

$sectionHeader = Header::fromBlueprint(
        $blueprint,
        $title
);

if(!$item->isHead()) {
    $backLink = URL::route($blueprint->getRouteName('getUpdate'), $item->getHeadId());
} else if($item->isDeleted()) {
    $backLink = URL::route($blueprint->getRouteName('getTrash'));
} else {
    $backLink = URL::route($blueprint->getRouteName('getList'));
}
$sectionHeader->setBackLink($backLink);

$itemHeader = Header::fromBlueprint($blueprint, $fields, ['model' => $item], Header::TYPE_NORMAL, 'item');

if(Auth::user()->hasPermissions($blueprint->getRouteName() . '.versions')) {
    $itemHeader->setSubtitle(getSubtitleForItem($item));
}

if(method_exists($item, 'isPublished')) {
    $icon = $item->isPublished() ? 'globe' : 'pencil-square';
    $itemHeader->setIcon($icon);
}

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