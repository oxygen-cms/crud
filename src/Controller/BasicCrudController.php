<?php

namespace Oxygen\Crud\Controller;

use View;
use Input;
use Lang;
use URL;
use Response;

use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Repository\ResourceRepository;
use Oxygen\Core\Blueprint\Manager as BlueprintManager;
use Oxygen\Core\Http\Notification;

use Oxygen\Core\Model\Validating\InvalidModelException;

class BasicCrudController extends ResourceController {

    /**
     * Constructs a BasicCrudController.
     *
     * @param BlueprintManager  $manager        BlueprintManager instance
     * @param string            $modelName      Name of the corresponding model
     * @param string            $blueprintName  Name of the corresponding Blueprint
     */

    public function __construct(BlueprintManager $manager, $blueprintName = null, $modelName = null) {
        parent::__construct($manager, $blueprintName, $modelName);

        Lang::when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
    }

    /**
     * List all items.
     *
     * @return Response
     */

    public function getList() {
        $items = $this->model->paginate(25);

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
            'item' => $this->model->newInstance()
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
            $item = $this->model->newInstance();
            $item->fill(Input::except(['_method', '_token']));
            $item->save();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.created')),
                ['redirect' => $this->blueprint->getRouteName('getList')]
            );
        } catch(InvalidModelException $e) {
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
            $item->fill(Input::except(['_method', '_token']));
            $item->save();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated'))
            );
        } catch(InvalidModelException $e) {
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
        try {
            $item = $this->getItem($item);
            $item->delete();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.deleted')),
                ['redirect' => $this->blueprint->getRouteName('getList')]
            );
        } catch(Exception $e) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.deleteFailed'))
            );
        }
    }

}