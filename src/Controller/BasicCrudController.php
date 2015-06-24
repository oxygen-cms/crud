<?php

namespace Oxygen\Crud\Controller;

use Exception;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Form\FieldSet;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;
use View;
use Input;
use Lang;
use URL;
use Response;

use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Blueprint\BlueprintManager as BlueprintManager;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Repository\RepositoryInterface;

class BasicCrudController extends ResourceController {

    /**
     * Form fields used in the Create/Read/Update/Delete actions
     *
     * @var \Oxygen\Core\Form\FieldSet
     */
    protected $crudFields;

    /**
     * Constructs a BasicCrudController.
     *
     * @param RepositoryInterface         $repository
     * @param Blueprint|BlueprintManager  $blueprint Blueprint or BlueprintManager
     */
    public function __construct(RepositoryInterface $repository, $blueprint, FieldSet $crudFields) {
        parent::__construct($repository, $blueprint);

        $this->crudFields = $crudFields;

        Lang::when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
        Lang::when('oxygen/crud::dialogs', ['resource' => $this->blueprint->getDisplayName()]);
        Lang::when('oxygen/crud::ui', ['resource' => $this->blueprint->getDisplayName(), 'pluralResource' => $this->blueprint->getDisplayName(Blueprint::PLURAL)]);
    }

    /**
     * List all items.
     *
     * @param QueryParameters $queryParameters
     * @return Response
     */
    public function getList(QueryParameters $queryParameters = null) {
        $items = $this->repository->paginate(25, $queryParameters == null ? new QueryParameters([], 'id', QueryParameters::DESCENDING) : $queryParameters);

        // render the view
        return View::make('oxygen/crud::basic.list', [
            'items' => $items,
            'isTrash' => false,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/crud::ui.resource.list')
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
            'item' => $item,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/crud::ui.resource.show')
        ]);
    }

    /**
     * Shows the create form.
     *
     * @return Response
     */
    public function getCreate() {
        return View::make('oxygen/crud::basic.create', [
            'item' => $this->repository->make(),
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/crud::ui.resource.create')
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
            'item' => $item,
            'fields' => $this->crudFields,
            'title' => Lang::get('oxygen/crud::ui.resource.update')
        ]);
    }

    /**
     * Creates a new Resource.
     *
     * @return Response
     */
    public function postCreate() {
        try {
            $item = $this->getItem($this->repository->make());
            $item->fromArray($this->transformInput(Input::except(['_method', '_token'])));
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
            $item->fromArray($this->transformInput(Input::except(['_method', '_token'])));
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

    /**
     * Transforms user input into data that can be applied to the model.
     *
     * @param array $input
     * @return array
     */
    public function transformInput($input) {
        foreach($input as $key => $value) {
            if($this->crudFields->hasField($key)) {
                $field = $this->crudFields->getField($key);
                $input[$key] = $field->getType()->transformInput($field, $value);
            }
        }
        return $input;
    }

}