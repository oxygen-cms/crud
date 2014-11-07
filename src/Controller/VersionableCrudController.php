<?php

namespace Oxygen\Crud\Controller;

use View;
use Response;
use Lang;
use Input;

use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;

use Exception;

class VersionableCrudController extends SoftDeleteCrudController {

    /**
     * List all entities.
     *
     * @param array $scopes
     * @return Response
     */

    public function getList($scopes = ['excludeTrashed', 'excludeVersions']) {
        return parent::getList($scopes);
    }

    /**
     * List all deleted entities.
     *
     * @param array $scopes
     * @return Response
     */

    public function getTrash($scopes = ['onlyTrashed', 'excludeVersions']) {
        return parent::getTrash($scopes);
    }

    /**
     * Shows info about an entity.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function getInfo($item) {
        $item = $this->getItem($item);

        return View::make('oxygen/crud::versionable.show', [
            'item' => $item
        ]);
    }

    /**
     * Shows the update form.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function getUpdate($item) {
        $item = $this->getItem($item);

        return View::make('oxygen/crud::versionable.update', [
            'item' => $item
        ]);
    }

    /**
     * Updates an entity.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function putUpdate($item) {
        try {
            $item = $this->getItem($item);
            $item->fromArray(Input::except(['_method', '_token', 'version']));
            $this->repository->persist($item, Input::get('version', 'guess'));

            $options = Input::get('version') === 'new' ? ['refresh' => true] : [];
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated')),
                $options
            );
        } catch(InvalidEntityException $e) {
            return Response::notification(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Makes a new version of an entity.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postNewVersion($item) {
        $item = $this->getItem($item);
        $this->repository->makeNewVersion($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.versionable.madeVersion')),
            ['refresh' => true]
        );
    }

    /**
     * Makes the version the head version.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postMakeHeadVersion($item) {
        $item = $this->getItem($item);

        if($item->isHead()) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.alreadyHead'), Notification::FAILED)
            );
        }

        $this->repository->makeHeadVersion($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.versionable.madeHead')),
            ['refresh' => true]
        );
    }

    /**
     * Clears all older versions of the item.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteVersions($item) {
        $item = $this->getItem($item);
        $entity = $this->repository->clearVersions($item);

        $options = ['redirect' => [$this->blueprint->getRouteName('getUpdate'), $entity->getId()]];
        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.versionable.clearedVersions')),
            $options
        );
    }

}