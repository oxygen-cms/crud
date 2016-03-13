<?php

namespace Oxygen\Crud\BlueprintTrait;

use Oxygen\Core\Html\Toolbar\ActionToolbarItem;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Http\Method;

class PreviewableCrudTrait implements BlueprintTraitInterface {

    /**
     * Register a set of actions that implement
     * Create, read, update and delete functionality.
     * Extends BasicCrudTrait with restore and forceDelete actions.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        $blueprint->makeAction([
            'name'      => 'getPreview',
            'pattern'   => '{id}/preview'
        ]);
        $blueprint->makeToolbarItem([
            'action'        => 'getPreview',
            'label'         => 'View',
            'icon'          => 'eye'
        ]);

        $blueprint->makeAction([
            'name'      => 'getContent',
            'pattern'   => '{id}/content'
        ]);
    }

}