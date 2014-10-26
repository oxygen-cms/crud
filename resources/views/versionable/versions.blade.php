<?php
    use Oxygen\Core\Html\Header\Header;
?>

@if(Auth::user()->hasPermissions($blueprint->getRouteName() . '.versions'))

<?php

    if(!function_exists('getSubtitleForItem')) {
        function getSubtitleForItem($item) {
            if($item->isHead()) {
                return Lang::get('oxygen/crud::ui.latestVersion');
            } else {
                return Lang::get('oxygen/crud::ui.fromDate', [
                    'date' => $item->updated_at->diffForHumans()
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
        $versions = $item->versions()->orderBy('updated_at', 'DESC')->get();
        $versions->prepend($item->getHead());

        if($versions->isEmpty()):
    ?>
        <h2 class="heading-gamma margin-large">
            @lang('oxygen/crud::ui.noItems')
        </h2>
    <?php
        endif;

        foreach($versions as $item):
            $itemHeader = Header::fromBlueprint(
                $blueprint,
                null,
                ['model' => $item],
                Header::TYPE_SMALL,
                'item'
            );

            $itemHeader->setSubtitle(getSubtitleForItem($item));

            echo $itemHeader->render();
        endforeach;
    ?>

</div>

@endif