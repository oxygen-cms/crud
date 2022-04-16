<?php
use Doctrine\Common\Collections\ArrayCollection;
use Oxygen\Core\Html\Header\Header;
use Oxygen\Auth\Permissions\Permissions;
?>

@if(app(Permissions::class)->has($blueprint->getRouteName() . '.versions'))

    <?php

    $getSubtitleForItem = function($version, $item = null) {
        if($version === $item) {
            return __('oxygen/crud::ui.thisVersion');
        } else if($version->isHead()) {
            $str = __('oxygen/crud::ui.latestVersion');
        } else {
            $str = __('oxygen/crud::ui.fromDate', [
                    'date' => $version->getUpdatedAt()->diffForHumans()
            ]);
        }
        if($version->getUpdatedBy() !== null) {
            $str = __('oxygen/crud::ui.modifiedBy', ['name' => $version->getUpdatedBy()->getFullName()]) . ' ' . $str;
        }
        return $str;
    };

    $header = Header::fromBlueprint(
            $blueprint,
            __('oxygen/crud::ui.versions'),
            ['model' => $item, 'statusIcon' => false],
            Header::TYPE_MAIN,
            'versionList'
    );

    ?>

    <div class="Block">
        {!! $header->render() !!}

        <?php
        $versions = $item->getVersions();
        $versions = new ArrayCollection(array_merge(
                [$item->getHead()],
                $versions->toArray()
        ));

        if($versions->isEmpty()):
        ?>
        <h2 class="heading-gamma margin-large">
            @lang('oxygen/crud::ui.noItems')
        </h2>
        <?php
        endif;

        foreach($versions as $version):
            $itemHeader = Header::fromBlueprint(
                    $blueprint,
                    $fields,
                    ['model' => $version],
                    Header::TYPE_SMALL,
                    'item'
            );

            $itemHeader->setSubtitle($getSubtitleForItem($version, $item));

            if(method_exists($version, 'isPublished')) {
                $icon = $version->isPublished() ? 'globe' : 'pencil-square';
                $itemHeader->setIcon($icon);
            }

            echo $itemHeader->render();
        endforeach;
        ?>

    </div>

@endif
