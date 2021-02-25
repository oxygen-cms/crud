<?php

namespace Oxygen\Crud\Controller;

use Oxygen\Data\Exception\InvalidEntityException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintManager as BlueprintManager;
use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Form\FieldSet;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Repository\QueryParameters;
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
     * @param RepositoryInterface        $repository
     * @param Blueprint|BlueprintManager $blueprint Blueprint or BlueprintManager
     */
    public function __construct(RepositoryInterface $repository, $blueprint, FieldSet $crudFields) {
        parent::__construct($repository, $blueprint);

        $this->crudFields = $crudFields;

        // automatically insert the crud fields to all views
        view()->share('crudFields', $this->crudFields);

        app('lang')->when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
        app('lang')->when('oxygen/crud::dialogs', ['resource' => $this->blueprint->getDisplayName()]);
        app('lang')->when('oxygen/crud::ui', ['resource' => $this->blueprint->getDisplayName(), 'pluralResource' => $this->blueprint->getPluralDisplayName()]);
    }

    /**
     * List all items.
     *
     * @param QueryParameters $queryParameters
     * @return \Illuminate\View\View
     */
    public function getList($queryParameters = null) {
        if($queryParameters == null) {
            $queryParameters = QueryParameters::make()
                ->orderBy('id', QueryParameters::DESCENDING);
        }
        $items = $this->repository->paginate(25, $queryParameters, null, app('request')->input('q', null));

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
     * @return \Illuminate\View\View
     */
    public function getInfo($item) {
        $item = $this->getItem($item);

        return view('oxygen/crud::basic.show')
            ->with('item', $item);
    }

    /**
     * Shows the create form.
     *
     * @return \Illuminate\View\View
     */
    public function getCreate() {
        return view('oxygen/crud::basic.create')
            ->with('item', $this->repository->make());
    }

    /**
     * Shows the update form.
     *
     * @param mixed $item the item
     * @return \Illuminate\View\View
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
     * @throws \Exception
     */
    public function postCreate(Request $input) {
        try {
            $item = $this->getItem($this->repository->make());
            $item->fromArray($this->transformInput($input->except(['_method', '_token'])));
            $this->repository->persist($item);

            return notify(new Notification(trans('oxygen/crud::messages.basic.created')), ['redirect' => $this->blueprint->getRouteName('getList')]);
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
     * @param Request $request
     * @param mixed $item the item
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function putUpdate(Request $request, $item) {
        try {
            $item = $this->getItem($item);
            $item->fromArray($this->transformInput($request->except(['_method', '_token'])));
            $this->repository->persist($item);

            return notify(
                new Notification(trans('oxygen/crud::messages.basic.updated'))
            );
        } catch(InvalidEntityException $e) {
            return notify(
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

        return notify(
            new Notification(trans('oxygen/crud::messages.basic.deleted')),
            ['redirect' => $this->blueprint->getRouteName('getList')]
        );
    }

    /**
     * Transforms user input into data that can be applied to the model.
     *
     * @param array $input
     * @return array
     * @throws \Exception
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
