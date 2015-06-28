<?php

namespace Oxygen\Crud\BlueprintTrait;

use Lang;

use Oxygen\Core\Html\Toolbar\ActionToolbarItem;
use Oxygen\Core\Html\Dialog\Dialog;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Http\Method;

class VersionableCrudTrait extends SoftDeleteCrudTrait implements BlueprintTraitInterface {

    /**
     * Constructs the SoftDeleteCrudTrait.
     *
     * @param array $options Extra options to be supplied to the trait.
     */
    public function __construct(array $options = []) {
        parent::__construct($options);
    }

    /**
     * Register a set of actions that implement
     * Create, read, update and delete functionality.
     * Extends BasicCrudTrait with restore and forceDelete actions.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        parent::applyTrait($blueprint);

        $noFilter = !isset($options['only']);

        if($noFilter || in_array('postNewVersion', $options['only'])) {
            $blueprint->makeAction([
                'name'      => 'postNewVersion',
                'pattern'   => '{id}/newVersion',
                'method'    => Method::POST
            ]);
            $blueprint->makeToolbarItem([
                'action'        => 'postNewVersion',
                'label'         => 'New Version',
                'icon'          => 'plus'
            ]);
        }

        if($noFilter || in_array('postMakeHeadVersion', $options['only'])) {
            $blueprint->makeAction([
                'name'      => 'postMakeHeadVersion',
                'pattern'   => '{id}/makeHead',
                'method'    => Method::POST
            ]);
            $blueprint->makeToolbarItem([
                'action'        => 'postMakeHeadVersion',
                'label'         => 'Make Head Version',
                'icon'          => 'flag',
                'shouldRenderCallback' => function(ActionToolbarItem $item, array $arguments) {
                    return
                        $item->shouldRenderBasic($arguments) &&
                        !$arguments['model']->isHead();
                }
            ]);
        }

        if($noFilter || in_array('postMakeHeadVersion', $options['only'])) {
            $blueprint->makeAction([
                'name'      => 'deleteVersions',
                'pattern'   => '{id}/versions',
                'method'    => Method::POST
            ]);
            $blueprint->makeToolbarItem([
                'action'        => 'deleteVersions',
                'label'         => 'Clear Versions',
                'icon'          => 'trash-o',
                'dialog'        => new Dialog(Lang::get('oxygen/crud::dialogs.versionable.clearVersions')),
                'shouldRenderCallback' => function(ActionToolbarItem $item, array $arguments) {
                    return
                        $item->shouldRenderBasic($arguments) &&
                        $arguments['model']->hasVersions();
                }
            ]);
        }

    }

}