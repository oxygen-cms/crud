<?php

namespace Oxygen\Crud\Controller;

use Lang;
use Input;

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

        return view('oxygen/crud::content.preview')
            ->with('item', $item);
    }

    /**
     * Renders the content for this resource as HTML.
     *
     * @param $item
     * @return Response
     */
    public function getContent($item) {
        $item = $this->getItem($item);

        // override the content
        if(Input::has('content')) {
            $content = Input::get('content');
            $path = view()->pathFromModel(get_class($item), $item->getId(), $this->crudFields->getContentFieldName());
            return view()->string($content, $path, 0);
        }

        return view()->model($item, $this->crudFields->getContentFieldName());
    }

}