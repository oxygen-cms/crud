<?php

namespace Oxygen\Crud\Controller;

use Oxygen\Data\Repository\QueryParameters;
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
     * @param QueryParameters $queryParameters
     * @return Response
     */
    public function getList(QueryParameters $queryParameters = null) {
        if($queryParameters == null) { $queryParameters = new QueryParameters(['excludeTrashed', 'excludeVersions'], 'id', QueryParameters::DESCENDING); }

        return parent::getList($queryParameters);
    }

    /**
     * List all deleted entities.
     *
     * @param QueryParameters $queryParameters
     * @return Response
     */
    public function getTrash(QueryParameters $queryParameters = null) {
        if($queryParameters == null) { $queryParameters = new QueryParameters(['onlyTrashed', 'excludeVersions'], 'id', QueryParameters::DESCENDING); }
        return parent::getTrash($queryParameters);
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
            'item' => $item,
            'title' => Lang::get('oxygen/crud::ui.resource.show')
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
            'item' => $item,
            'title' => Lang::get('oxygen/crud::ui.resource.update')
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
            $item->fromArray($this->transformInput(Input::except(['_method', '_token', 'version'])));
            $madeNewVersion = $this->repository->persist($item, Input::get('version', 'guess'));

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated')),
                ['refresh' => $madeNewVersion]
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