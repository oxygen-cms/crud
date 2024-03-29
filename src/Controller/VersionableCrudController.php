<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\Doctrine\Versions;
use Oxygen\Data\Repository\QueryParameters;
use Webmozart\Assert\Assert;

class VersionableCrudController extends SoftDeleteCrudController {

    /**
     * Shows the update form.
     *
     * @param mixed $item the item
     * @return \Illuminate\View\View
     */
    public function getUpdate($item) {
        $item = $this->getItem($item);

        return view('oxygen/crud::versionable.update')
            ->with('item', $item);
    }

    /**
     * Updates an entity.
     *
     * @param Request $request
     * @param mixed $item the item
     * @return Response
     * @throws \Exception
     */
    public function putUpdate(Request $request, $item) {
        try {
            $item = $this->getItem($item);
            $item->fromArray($this->transformInput($request->except(['_method', '_token', 'version'])));
            $madeNewVersion = $this->repository->persist($item, true, $request->input('version', 'guess'));

            return notify(
                new Notification(__('oxygen/crud::messages.basic.updated')),
                ['refresh' => $madeNewVersion]
            );
        } catch(InvalidEntityException $e) {
            return notify(
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

        return notify(
            new Notification(__('oxygen/crud::messages.versionable.madeVersion')),
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
            return notify(
                new Notification(__('oxygen/crud::messages.versionable.alreadyHead'), Notification::FAILED)
            );
        }

        $this->repository->makeHeadVersion($item);

        return notify(
            new Notification(__('oxygen/crud::messages.versionable.madeHead')),
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

        return notify(
            new Notification(__('oxygen/crud::messages.versionable.clearedVersions')),
            $options
        );
    }

}
