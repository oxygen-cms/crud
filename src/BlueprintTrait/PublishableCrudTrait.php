<?php

namespace Oxygen\Crud\BlueprintTrait;

use Oxygen\Core\Html\Toolbar\ActionToolbarItem;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Blueprint\Blueprint;

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
            'name'      => 'postPublish',
            'pattern'   => '{id}/publish',
            'method'    => 'POST'
        ]);
        $blueprint->makeToolbarItem([
            'action'        => 'postPublish',
            'label'         => 'Publish',
            'icon'          => 'globe',
        ])->addDynamicCallback(function(ActionToolbarItem $item, array $arguments) {
            if($arguments['model']->isPublished()) {
                $item->label = 'Unpublish';
                $item->icon = 'archive';
            }
        });

        $blueprint->makeAction([
            'name'      => 'postMakeDraft',
            'pattern'   => '{id}/makeDraft',
            'method'    => 'POST'
        ]);
        $blueprint->makeToolbarItem([
            'action'        => 'postMakeDraft',
            'label'         => 'Make Draft',
            'icon'          => 'pencil',
        ]);

        $blueprint->makeField([
            'name'      => 'stage',
            'type'      => 'select',
            'editable'  => true,
        ]);
    }

}