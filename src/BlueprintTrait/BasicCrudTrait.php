<?php

namespace Oxygen\Crud\BlueprintTrait;

use Auth;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Http\Method;

class BasicCrudTrait implements BlueprintTraitInterface {

    /**
     * Constructs the BasicCrudTrait.
     *
     * @param array $options Extra options to be supplied to the trait.
     */
    public function __construct(array $options = []) {
        $this->options = $options;
    }

    /**
     * Register a set of actions that implement
     * Create, read, update and delete functionality.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        $noFilter = !isset($options['only']);

        if($noFilter || in_array('getList', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'getList',
                'pattern' => '/'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getList',
                'label' => $blueprint->getPluralDisplayName(),
                'icon' => $blueprint->getIcon(),
                'color' => 'green'
            ]);
            $blueprint->setPrimaryToolbarItem('getList');
        }

        if($noFilter || in_array('getCreate', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'getCreate',
                'pattern' => 'create'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getCreate',
                'label' => 'Create ' . $blueprint->getDisplayName(),
                'icon' => 'edit',
                'color' => 'green'
            ]);
        }

        if($noFilter || in_array('postCreate', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'postCreate',
                'pattern' => '/',
                'method' => Method::POST
            ]);
        }

        if($noFilter || in_array('getTrash', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'getTrash',
                'pattern' => 'trash'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getTrash',
                'label' => 'Trash',
                'icon' => 'trash-o',
                'color' => 'grey'
            ]);
        }

        if($noFilter || in_array('getUpdate', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'getUpdate',
                'pattern' => '{id}/edit'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getUpdate',
                'label' => 'Edit',
                'icon' => 'pencil'
            ]);
        }

        if($noFilter || in_array('putUpdate', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'putUpdate',
                'pattern' => '{id}',
                'method' => Method::PUT
            ]);
        }

        if($noFilter || in_array('getInfo', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'getInfo',
                'pattern' => '{id}'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getInfo',
                'label' => 'Show Info',
                'icon' => 'search'
            ]);
        }

        if($noFilter || in_array('deleteDelete', $options['only'])) {
            $blueprint->makeAction([
                'name' => 'deleteDelete',
                'pattern' => '{id}',
                'method' => Method::DELETE
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'deleteDelete',
                'label' => 'Delete',
                'icon' => 'trash-o'
            ]);
        }
    }

}