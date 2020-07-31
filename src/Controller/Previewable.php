<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\Response;
use Illuminate\View\View;
use Oxygen\Core\Templating\TwigTemplateCompiler;
use Twig\Error\Error;

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
     * @return View
     */
    public function getPreview($item) {
        $item = $this->getItem($item);

        return view('oxygen/crud::content.preview')
            ->with('item', $item);
    }

    /**
     * Renders custom content as HTML.
     *
     * @param TwigTemplateCompiler $templating
     * @return Response|View
     */
    public function postContent(TwigTemplateCompiler $templating) {
        $content = request()->get('content', '');
        if(!$content) {
            $content = '';
        }
        try {
            $rendered = $templating->renderString($content, 'content');
            return $this->decoratePreviewContent($rendered);
        } catch(Error $e) {
            return response($e->getMessage());
        }
    }

    /**
     * Renders this resource as HTML
     *
     * @param object $item
     * @param TwigTemplateCompiler $templating
     * @return View
     */
    public function getContent($item, TwigTemplateCompiler $templating) {
        $item = $this->getItem($item);

        $content = $templating->render($item);

        if(method_exists($this, 'decorateContent')) {
            return $this->decorateContent($content, $item);
        } else {
            return $this->decoratePreviewContent($content);
        }
    }
}
