<?php

namespace Oxygen\Crud\BlueprintTrait;

use Lang;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintTraitInterface;
use Oxygen\Core\Html\Toolbar\Factory\FormToolbarItemFactory;
use Oxygen\Core\Form\FieldMetadata;

class SearchableCrudTrait implements BlueprintTraitInterface {

    /**
     * Register a set of actions that implement
     * Create, read, update and delete functionality.
     * Extends BasicCrudTrait with restore and forceDelete actions.
     *
     * @param Blueprint $blueprint
     */
    public function applyTrait(Blueprint $blueprint) {
        $blueprint->makeToolbarItem([
            'action' => 'getList',
            'identifier' => 'getList.search',
            'fields' => function() use($blueprint) {
                $query = new FieldMetadata('q', 'search', true);
                $query->label = 'Query';
                $query->placeholder = Lang::get('oxygen/crud::ui.resource.search', [
                    'name' => $blueprint->getDisplayName(),
                    'pluralName' => $blueprint->getPluralDisplayName()
                ]);
                $query->attributes['results'] = 5;

                return [
                    $query
                ];
            }
        ], new FormToolbarItemFactory());
    }

}
