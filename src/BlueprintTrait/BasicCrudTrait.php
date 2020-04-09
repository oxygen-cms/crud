<?php

namespace Oxygen\Crud\BlueprintTrait;

use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Http\Method;

class BasicCrudTrait implements BlueprintTraitInterface {

    /**
     * @var array
     */
    protected $options;

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
        $noFilter = !isset($this->options['only']);

        if($noFilter || in_array('getList', $this->options['only'])) {
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

        if($noFilter || in_array('getCreate', $this->options['only'])) {
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

        if($noFilter || in_array('postCreate', $this->options['only'])) {
            $blueprint->makeAction([
                'name' => 'postCreate',
                'pattern' => '/',
                'method' => Method::POST
            ]);
        }

        if($noFilter || in_array('getTrash', $this->options['only'])) {
            $blueprint->makeAction([
                'name' => 'getTrash',
                'pattern' => 'trash'
            ]);
            $blueprint->makeToolbarItem([
                'action' => 'getTrash',
                'label' => 'Trash',
                'icon' => 'trash-o',
                'color' => 'dark-grey'
            ]);
        }

        if($noFilter || in_array('getUpdate', $this->options['only'])) {
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

        if($noFilter || in_array('putUpdate', $this->options['only'])) {
            $blueprint->makeAction([
                'name' => 'putUpdate',
                'pattern' => '{id}',
                'method' => Method::PUT
            ]);
        }

        if($noFilter || in_array('getInfo', $this->options['only'])) {
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

        if($noFilter || in_array('deleteDelete', $this->options['only'])) {
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