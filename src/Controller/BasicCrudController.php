<?php

namespace Oxygen\Crud\Controller;

use Exception;
use Oxygen\Data\Exception\InvalidEntityException;
use View;
use Input;
use Lang;
use URL;
use Response;

use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Blueprint\Manager as BlueprintManager;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Repository\RepositoryInterface;

class BasicCrudController extends ResourceController {

    /**
     * Constructs a BasicCrudController.
     *
     * @param RepositoryInterface $repository
     * @param BlueprintManager    $manager       BlueprintManager instance
     * @param string              $blueprintName Name of the corresponding Blueprint
     */

    public function __construct(RepositoryInterface $repository, BlueprintManager $manager, $blueprintName = null) {
        parent::__construct($repository, $manager, $blueprintName);

        Lang::when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
        Lang::when('oxygen/crud::dialogs', ['resource' => $this->blueprint->getDisplayName()]);
    }

    /**
     * List all items.
     *
     * @param array $scopes
     * @return Response
     */

    public function getList($scopes = []) {
        $items = $this->repository->paginate(25, $scopes);

        // render the view
        return View::make('oxygen/crud::basic.list', [
            'items' => $items,
            'isTrash' => false
        ]);
    }

    /**
     * Shows info about a Resource.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function getInfo($item) {
        $item = $this->getItem($item);

        return View::make('oxygen/crud::basic.show', [
            'item' => $item
        ]);
    }

    /**
     * Shows the create form.
     *
     * @return Response
     */

    public function getCreate() {
        return View::make('oxygen/crud::basic.create', [
            'item' => $this->repository->make()
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

        return View::make('oxygen/crud::basic.update', [
            'item' => $item
        ]);
    }

    /**
     * Creates a new Resource.
     *
     * @return Response
     */

    public function postCreate() {
        try {
            $item = $this->repository->make();
            $item->fromArray(Input::except(['_method', '_token']));
            $this->repository->persist($item);

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.created')),
                ['redirect' => $this->blueprint->getRouteName('getList')]
            );
        } catch(InvalidEntityException $e) {
            return Response::notification(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Updates a Resource.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function putUpdate($item) {
        try {
            $item = $this->getItem($item);
            $item->fromArray(Input::except(['_method', '_token']));
            $this->repository->persist($item);

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated'))
            );
        } catch(InvalidEntityException $e) {
            return Response::notification(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Deletes a Resource.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteDelete($item) {
        $item = $this->getItem($item);
        $this->repository->delete($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.basic.deleted')),
            ['redirect' => $this->blueprint->getRouteName('getList')]
        );
    }

}