<?php

namespace Oxygen\Crud\Controller;

use Input;
use Lang;

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
     * Renders custom content as HTML.
     *
     * @param $item
     * @return Response
     */
    public function postContent() {
        $path = view()->pathFromModel('unkown', 0, $this->crudFields->getContentFieldName());

        $content = Input::get('content', '');
        return $this->decoratePreviewContent(view()->string($content, $path, 0));
    }

    /**
     * Renders this resource as HTML
     *
     * @param $item
     * @return Response
     */
    public function getContent($item) {
        $item = $this->getItem($item);
        $content = view()->model($item, $this->crudFields->getContentFieldName());
        if(method_exists($this, 'decorateContent')) {
            return $this->decorateContent($content, $item);
        } else {
            return $this->decoratePreviewContent($content);
        }
    }
}
