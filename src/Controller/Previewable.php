<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Oxygen\Core\Templating\TwigTemplateCompiler;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * The Previewable trait extends a Versionable resource,
 * and adds a page that shows some 'content' of the resource rendered as HTML.
 *
 * @package Oxygen\Crud\Controller
 */
trait Previewable {

    /**
     * Preview the item.
     *
     * @param mixed $item
     * @return View
     */
    public function getPreview($item) {
        return redirect()->to(route($this->blueprint->getRouteName() . '.getUpdate', ['id' => $item]) . '?mode=preview');
    }

    /**
     * Renders custom content as HTML.
     *
     * @param TwigTemplateCompiler $templating
     * @param Request $request
     * @param null $item
     * @return Response|View
     */
    public function postContent(TwigTemplateCompiler $templating, Request $request, $item = null) {
        try {
            $content = $request->get('content', null);
            if(!$content && !$item) {
                $content = '';
            }

            return $this->getContent($templating, $content, $request->get('renderLayout', 'true') === 'true', $item);
        } catch(Error $e) {
            return response($e->getMessage());
        }
    }

    /**
     * Renders this resource as HTML
     *
     * @param TwigTemplateCompiler $templating
     * @param string|null $contentOverride
     * @param bool $renderLayout
     * @param object|null $item
     * @return View
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function getContent(TwigTemplateCompiler $templating, ?string $contentOverride, bool $renderLayout, $item) {
        if($item !== null) {
            $item = $this->getItem($item);
        }

        if($contentOverride !== null) {
            $rendered = $templating->renderString($contentOverride, $item);
        } else {
            $rendered = $templating->render($item);
        }

        if(method_exists($this, 'decorateContent') && $renderLayout) {
            return $this->decorateContent($rendered, $item);
        } else {
            return $this->decoratePreviewContent($rendered);
        }
    }
}
