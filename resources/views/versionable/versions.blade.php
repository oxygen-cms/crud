<?php
    use Doctrine\Common\Collections\ArrayCollection;use Oxygen\Core\Html\Header\Header;
?>

@if(Auth::user()->hasPermissions($blueprint->getRouteName() . '.versions'))

<?php

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

    $header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/crud::ui.versions'),
        ['model' => $item],
        Header::TYPE_MAIN,
        'versionList'
    );

?>

<div class="Block">
    {{ $header->render() }}
</div>

<div class="Block">

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
                null,
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