<?php

namespace Oxygen\Crud\Controller;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Behaviour\Versionable;
use Oxygen\Data\Exception\InvalidEntityException;
use Illuminate\Http\Response;

trait Publishable {

    // TODO: delete this once we get rid of old blade-template-based UI
    /**
     * Publish an entity.
     *
     * @param mixed $item the item
     * @return Response
     */
    public function postPublish($item) {
        try {
            $item = $this->getItem($item);
            $item->publish();
            $this->repository->persist($item, true, 'overwrite');

            return notify(
                new Notification(
                    __('oxygen/crud::messages.publishable.published')
                ),
                ['refresh' => true]
            );
        } catch(InvalidEntityException $e) {
            return notify(
                new Notification($e->getErrors()->first(), Notification::FAILED)
            );
        }
    }

    // TODO: delete this once we get rid of old blade-template-based UI
    /**
     * Unpublish an entity.
     *
     * @param mixed $item the item
     * @return Response
     */
    public function postUnpublish($item) {
        try {
            $item = $this->getItem($item);
            $item->unpublish();
            $this->repository->persist($item, true, 'overwrite');

            return notify(
                new Notification(
                    __('oxygen/crud::messages.publishable.unpublished')
                ),
                ['refresh' => true]
            );
        } catch (InvalidEntityException $e) {
            return notify(
                new Notification($e->getErrors()->first(), Notification::FAILED)
            );
        }
    }

    /**
     * Publish or unpublish an entity.
     *
     * @param mixed $item the item
     * @return JsonResponse
     * @throws InvalidEntityException
     */
    public function publish($item): JsonResponse {
        $item = $this->getItem($item);
        $item->publish();
        $this->repository->persist($item, true, 'overwrite');

        return response()->json(['item' => $item->toArray(), 'status' => Notification::SUCCESS, 'content' => 'Successfully published']);
    }

    // TODO: make this work with putUpdateApi instead...
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

    /**
     * Registers API routes.
     *
     * @param Router $router
     */
    public static function registerPublishableRoutes(Router $router) {
        $resourceName = explode('/', $router->getLastGroupPrefix());
        $resourceName = Str::camel(last($resourceName));
        $router->post("/{id}/publish", static::class . "@publish")
            ->name("$resourceName.postPublishApi")
            ->middleware("oxygen.permissions:$resourceName.postPublish");
    }

}
