<?php

use Oxygen\Auth\Permissions\Permissions;
use Oxygen\Core\Html\Header\Header;

$getSubtitleForItem = function($version, $item = null) {
    if($version === $item) {
        return __('oxygen/crud::ui.thisVersion');
    } else if($version->isHead()) {
        return __('oxygen/crud::ui.latestVersion');
    } else {
        return __('oxygen/crud::ui.fromDate', [
            'date' => $version->getUpdatedAt()->diffForHumans()
        ]);
    }
};

$sectionHeader = Header::fromBlueprint(
    $blueprint,
    $title
);

if(!$item->isHead()) {
    $backLink = url()->route($blueprint->getRouteName('getUpdate'), $item->getHeadId());
} else if($item->isDeleted()) {
    $backLink = url()->route($blueprint->getRouteName('getTrash'));
} else {
    $backLink = url()->route($blueprint->getRouteName('getList'));
}
$sectionHeader->setBackLink($backLink);

$itemHeader = Header::fromBlueprint($blueprint, $fields, ['model' => $item], Header::TYPE_NORMAL, 'item');

if(app(Permissions::class)->has($blueprint->getRouteName() . '.versions')) {
    $itemHeader->setSubtitle($getSubtitleForItem($item));
}

echo $sectionHeader->render();
echo $itemHeader->render();
