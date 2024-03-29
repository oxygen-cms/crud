<?php

namespace Oxygen\Crud\BlueprintTrait;

use Illuminate\Support\Facades\Lang;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Html\Dialog\Dialog;
use Oxygen\Core\Html\Toolbar\ActionToolbarItem;
use Oxygen\Core\Http\Method;
use Webmozart\Assert\Assert;

class SoftDeleteCrudTrait extends BasicCrudTrait implements BlueprintTraitInterface {

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
     * create, read, update and delete functionality. (CRUD)
     * Extends BasicCrudTrait with restore and forceDelete actions.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        parent::applyTrait($blueprint);

        $noFilter = !isset($this->options['only']);

        if($noFilter || in_array('deleteDelete', $this->options['only'])) {
            $tb = $blueprint->getToolbarItem('deleteDelete');
            Assert::isInstanceOf($tb, ActionToolbarItem::class);
            $tb->shouldRenderCallback = function (ActionToolbarItem $item, array $arguments) {
                return
                    $item->shouldRenderBasic($arguments) &&
                    !$arguments['model']->isDeleted();
            };
        }

        if($noFilter || in_array('postRestore', $this->options['only'])) {
            $blueprint->makeAction([
                'name' => 'postRestore',
                'pattern' => '{id}/restore',
                'method' => Method::POST
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'postRestore',
                'label' => 'Restore',
                'icon' => 'undo',
                'shouldRenderCallback' => function (ActionToolbarItem $item, array $arguments) {
                    return
                        $item->shouldRenderBasic($arguments) &&
                        $arguments['model']->isDeleted();
                }
            ]);
        }

        if($noFilter || in_array('deleteForce', $this->options['only'])) {
            $blueprint->makeAction([
                'name' => 'deleteForce',
                'pattern' => '{id}/force',
                'method' => Method::DELETE
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'deleteForce',
                'label' => 'Force Delete',
                'icon' => 'trash-o',
                'dialog' => new Dialog(Lang::get('oxygen/crud::dialogs.softDelete.forceDelete')),
                'shouldRenderCallback' => function (ActionToolbarItem $item, array $arguments) {
                    return
                        $item->shouldRenderBasic($arguments) &&
                        $arguments['model']->isDeleted();
                }
            ]);
        }
    }

}