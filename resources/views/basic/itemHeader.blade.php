<?php

use Oxygen\Core\Html\Header\Header;
use Oxygen\Data\Behaviour\StatusIconInterface;

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

echo $sectionHeader->render();
echo $itemHeader->render();