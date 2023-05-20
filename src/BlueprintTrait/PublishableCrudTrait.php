<?php

namespace Oxygen\Crud\BlueprintTrait;

use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Html\Toolbar\ActionToolbarItem;
use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
use Oxygen\Core\Http\Method;
use Webmozart\Assert\Assert;

class PublishableCrudTrait implements BlueprintTraitInterface {

    /**
     * Register a set of actions that implement
     * Create, read, update and delete functionality.
     * Extends BasicCrudTrait with restore and forceDelete actions.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        $blueprint->makeAction([
            'name' => 'postPublish',
            'pattern' => '{id}/publish',
            'method' => Method::POST
        ]);
        $blueprint->makeToolbarItem([
            'action' => 'postPublish',
            'label' => 'Publish',
            'color' => 'blue',
            'icon' => 'globe',
            'shouldRenderCallback' => function(ActionToolbarItem $item, array $arguments) {
                if($arguments['model']->isPublished()) {
                    return false;
                }
                return $item->shouldRenderBasic($arguments);
            }
        ]);
        $blueprint->makeAction([
            'name' => 'postUnpublish',
            'pattern' => '{id}/unpublish',
            'method' => Method::POST
        ]);
        $blueprint->makeToolbarItem([
            'action' => 'postUnpublish',
            'label' => 'Unpublish',
            'color' => 'white',
            'icon' => 'archive',
            'shouldRenderCallback' => function(ActionToolbarItem $item, array $arguments) {
                if(!$arguments['model']->isPublished()) {
                    return false;
                }
                return $item->shouldRenderBasic($arguments);
            }
        ]);

        $blueprint->makeAction([
            'name' => 'postMakeDraft',
            'pattern' => '{id}/makeDraft',
            'method' => Method::POST
        ]);
        $blueprint->makeToolbarItem([
            'action' => 'postMakeDraft',
            'label' => 'Make Draft',
            'icon' => 'pencil',
        ]);
    }

}
