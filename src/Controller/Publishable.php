<?php

namespace Oxygen\Crud\Controller;

use Oxygen\Core\Html\Dialog\Dialog;
use Oxygen\Core\Html\Form\Form;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;

trait Publishable {

    /**
     * Publish or unpublish an entity.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
     */
    public function postPublish($item) {
        try {
            $item = $this->getItem($item);
            $item->isPublished() ? $item->unpublish() : $item->publish();
            $this->repository->persist($item, true, 'overwrite');

            return notify(
                new Notification(
                    __($item->isPublished() ? 'oxygen/crud::messages.publishable.published' : 'oxygen/crud::messages.publishable.unpublished')
                ),
                ['refresh' => true]
            );
        } catch(InvalidEntityException $e) {
            return notify(
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
            return notify(new Notification(
                __('oxygen/crud::messages.publishable.alreadyDraft'),
                Notification::FAILED
            ));
        }

        $this->repository->makeDraftOfVersion($item);

        return notify(
            new Notification(
                __('oxygen/crud::messages.publishable.publishedSoMadeDraft')
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
            Event::listen('oxygen.layout.page.after', function () use ($item) {
                $dialog = new Dialog(__('oxygen/crud::dialogs.publishable.makeDraft'));
                $buttonAttributes = array_merge(
                    ['type' => 'submit'],
                    $dialog->render()
                );
                $form = new Form($this->blueprint->getAction('postMakeDraft'));
                $form->setAsynchronous(true);
                $form->addClass('Form--autoSubmit');
                $form->addClass('Form--hidden');
                $form->setRouteParameterArguments(['model' => $item]);

                $form->addContent('<button ' . html_attributes($buttonAttributes) . '>Submit</button>');
                echo $form->render();
            });
        }

        return parent::getUpdate($item);
    }

}
