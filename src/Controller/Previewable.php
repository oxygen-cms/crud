<?php
    
namespace Oxygen\Crud\Controller;

/**
 * The Previewable trait extends a Versionable resource,
 * and adds a page that shows some 'content' of the resource rendered as HTML.
 *
 * @package Oxygen\Crud\Controller
 */
trait Previewable {

    /**
     * Preview the page.
     *
     * @param mixed $item
     * @return Response
     */
    public function getPreview($item) {
        $item = $this->getItem($item);

        return View::make('oxygen/crud::content.preview', [
            'item' => $item,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/crud::ui.resource.preview', [
                'name' => $item->getAttribute($this->crudFields->getTitleFieldName())
            ])
        ]);
    }
    
}