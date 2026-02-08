<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\RedirectResponse;
use Oxygen\Core\Support\Str;
use \ReflectionClass;
use Illuminate\Translation\Translator;
use Oxygen\Data\Behaviour\Searchable;
use Oxygen\Data\Exception\InvalidEntityException;
use Illuminate\Http\Request;
use Oxygen\Core\Blueprint\Blueprint;
use Oxygen\Core\Blueprint\BlueprintManager as BlueprintManager;
use Oxygen\Core\Controller\ResourceController;
use Oxygen\Core\Form\FieldSet;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Repository\QueryParameters;
use Oxygen\Data\Repository\RepositoryInterface;
use Oxygen\Data\Repository\SearchMultipleFieldsClause;

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

        app(Translator::class)->when('oxygen/crud::messages', ['resource' => $this->blueprint->getDisplayName()]);
        app(Translator::class)->when('oxygen/crud::dialogs', ['resource' => $this->blueprint->getDisplayName()]);
        app(Translator::class)->when('oxygen/crud::ui', ['resource' => $this->blueprint->getDisplayName(), 'pluralResource' => $this->blueprint->getPluralDisplayName()]);
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

    /**
     * Redirects to the new Vue.js UI
     * @return RedirectResponse
     */
    public function getList()
    {
        $slug = Str::slug(Str::camelToWords($this->blueprint->getPluralName()));
        return redirect()->away('/oxygen/' . $slug);
    }

}
