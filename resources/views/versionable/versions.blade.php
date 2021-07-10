<?php
use Doctrine\Common\Collections\ArrayCollection;
use Oxygen\Core\Html\Header\Header;
use Oxygen\Auth\Permissions\Permissions;
?>

@if(app(Permissions::class)->has($blueprint->getRouteName() . '.versions'))

    <?php

    if(!function_exists('getSubtitleForItem')) {
        function getSubtitleForItem($version, $item = null) {
            if($version === $item) {
                return __('oxygen/crud::ui.thisVersion');
            } else if($version->isHead()) {
                return __('oxygen/crud::ui.latestVersion');
            } else {
                return __('oxygen/crud::ui.fromDate', [
                        'date' => $version->getUpdatedAt()->diffForHumans()
                ]);
            }
        }
    }

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

            $itemHeader->setSubtitle(getSubtitleForItem($version, $item));

            if(method_exists($version, 'isPublished')) {
                $icon = $version->isPublished() ? 'globe' : 'pencil-square';
                $itemHeader->setIcon($icon);
            }

            echo $itemHeader->render();
        endforeach;
        ?>

    </div>

@endif
