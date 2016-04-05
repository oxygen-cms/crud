<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\Request;
use Input;
use Lang;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintManager as BlueprintManager;
use Oxygen\Core\Contracts\Routing\ResponseFactory;
use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Form\FieldSet;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;
use Oxygen\Data\Repository\RepositoryInterface;
use Response;
use URL;
use View;

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
     * @param RepositoryInterface        $repository
     * @param Blueprint|BlueprintManager $blueprint Blueprint or BlueprintManager
     */
    public function __construct(RepositoryInterface $repository, $blueprint, FieldSet $crudFields) {
        parent::__construct($repository, $blueprint);

        $this->crudFields = $crudFields;

        // automatically insert the crud fields to all views
        view()->share('crudFields', $this->crudFields);

        Lang::when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
        Lang::when('oxygen/crud::dialogs', ['resource' => $this->blueprint->getDisplayName()]);
        Lang::when('oxygen/crud::ui', ['resource' => $this->blueprint->getDisplayName(), 'pluralResource' => $this->blueprint->getPluralDisplayName()]);
    }

    /**
     * List all items.
     *
     * @param QueryParameters $queryParameters
     * @return \Illuminate\Http\Response
     */
    public function getList($queryParameters = null) {
        $items = $this->repository->paginate(25, $queryParameters == null ? new QueryParameters([], 'id', QueryParameters::DESCENDING) : $queryParameters);

        // render the list
        return view('oxygen/crud::basic.list')
            ->with([
                'items' => $items,
                'isTrash' => false
            ]);
    }

    /**
     * Shows info about a Resource.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
     */
    public function getInfo($item) {
        $item = $this->getItem($item);

        return view('oxygen/crud::basic.show')
            ->with('item', $item);
    }

    /**
     * Shows the create form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate() {
        return view('oxygen/crud::basic.create')
            ->with('item', $this->repository->make());
    }

    /**
     * Shows the update form.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
     */
    public function getUpdate($item) {
        $item = $this->getItem($item);

        return view('oxygen/crud::basic.update')
            ->with('item', $item);
    }

    /**
     * Creates a new Resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $input) {
        try {
            $item = $this->getItem($this->repository->make());
            $item->fromArray($this->transformInput($input->except(['_method', '_token'])));
            $this->repository->persist($item);

            return notify(new Notification(Lang::get('oxygen/crud::messages.basic.created')), ['redirect' => $this->blueprint->getRouteName('getList')]);
        } catch(InvalidEntityException $e) {
            return notify(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Updates a Resource.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
     */
    public function putUpdate($item, ResponseFactory $response) {
        try {
            $item = $this->getItem($item);
            $item->fromArray($this->transformInput(Input::except(['_method', '_token'])));
            $this->repository->persist($item);

            return $response->notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated'))
            );
        } catch(InvalidEntityException $e) {
            return $response->notification(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Deletes a Resource.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
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