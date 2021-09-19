<?php

namespace Oxygen\Crud\Controller;

use Exception;
use Illuminate\Http\Request;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Behaviour\Versionable;
use Oxygen\Data\Exception\InvalidEntityException;
use Illuminate\Http\Response;

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
     * Updates an entity.
     *
     * @param Request $request
     * @param mixed $item the item
     * @return Response
     */
    public function putUpdate(Request $request, $item) {
        try {
            $item = $this->getItem($item);

            $shouldRefresh = false;
            $userInput = $request->except(['_method', '_token', 'version']);
            $stage = isset($userInput['stage']) ? (int) $userInput['stage'] : $item->getStage();
            $createNewVersion = $request->input('version', 'guess');
            if($item->isPublished() && $stage !== \Oxygen\Data\Behaviour\Publishable::STAGE_DRAFT) {
                $this->repository->makeDraftOfVersion($item, false);
                unset($userInput['stage']);
                $item->setStage(\Oxygen\Data\Behaviour\Publishable::STAGE_DRAFT);
                $createNewVersion = Versionable::NO_NEW_VERSION; // we just created a new version!! don't want to make too many
                $shouldRefresh = true;
            }

            $item->fromArray($this->transformInput($userInput));
            if($this->repository->persist($item, true, $createNewVersion)) {
                $shouldRefresh = true;
            }

            return notify(
                new Notification(__('oxygen/crud::messages.basic.updated')),
                ['refresh' => $shouldRefresh]
            );
        } catch(InvalidEntityException $e) {
            return notify(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        } catch(Exception $e) {
            report($e);
            logger()->error($e);
            logger()->error($e->getPrevious());
            return notify(
                new Notification('PHP Error in Page Content', Notification::FAILED),
                ['input' => true]
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

}
