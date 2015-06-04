<?php

namespace Oxygen\Crud\Controller;

use Event;
use Form;
use Lang;
use Oxygen\Core\Html\Dialog\Dialog;
use Oxygen\Data\Exception\InvalidEntityException;
use Response;
use Oxygen\Core\Http\Notification;
use URL;
use View;

trait Publishable {

    /**
     * Publish or unpublish an entity.
     *
     * @param mixed $item the item
     * @return Response
     */
    public function postPublish($item) {
        try {
            $item = $this->getItem($item);
            $item->isPublished() ? $item->unpublish() : $item->publish();
            $this->repository->persist($item, 'overwrite');

            return Response::notification(
                new Notification(
                    Lang::get($item->isPublished() ? 'oxygen/crud::messages.publishable.published' : 'oxygen/crud::messages.publishable.unpublished')
                ),
                ['refresh' => true]
            );
        } catch(InvalidEntityException $e) {
            return Response::notification(
                new Notification($e->getErrors()->first(), Notification::FAILED)
            );
        }
    }

    /**
     * Make a new version of the entity, and then change it to a draft.
     *
     * @param mixed $item the item
     * @return Response
     */
    public function postMakeDraft($item) {
        $item = $this->getItem($item);

        if(!$item->isPublished()) {
            return Response::notification(new Notification(
                Lang::get('oxygen/crud::messages.publishable.alreadyDraft'),
                Notification::FAILED
            ));
        }

        $this->repository->makeDraftOfVersion($item);

        return Response::notification(
            new Notification(
                Lang::get('oxygen/crud::messages.publishable.publishedSoMadeDraft')
            ),
            ['refresh' => true]
        );
    }

    /**
     * Shows the update form.
     *
     * @param mixed $item the item
     * @return Response
     */
    public function getUpdate($item) {
        $item = $this->getItem($item);

        if($item->isPublished()) {
            Event::listen('oxygen.layout.page.after', function() use($item) {
                echo View::make('oxygen/crud::publishable.makeDraftDialog', ['item' => $item])->render();
            });
        }

        return parent::getUpdate($item);
    }

}