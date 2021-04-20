<?php


namespace Oxygen\Crud\Controller;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Lang;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Behaviour\Searchable;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;
use Oxygen\Data\Repository\SearchMultipleFieldsClause;
use ReflectionClass;

trait BasicCrudApi {

    /**
     * List all entities.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function getListApi(Request $request): JsonResponse {
        $paginator = $this->repository->paginate(self::PER_PAGE, $this->getListQueryParameters($request));

        // render the list
        return response()->json([
            'items' => array_map(function($item) { return $item->toArray(); }, $paginator->items()),
            'totalItems' => $paginator->total(),
            'itemsPerPage' => $paginator->perPage(),
            'status' => Notification::SUCCESS,
        ]);
    }

    /**
     * @param QueryParameters $queryParameters
     * @param Request $request
     * @throws \ReflectionException
     */
    protected function maybeAddSearchClause(QueryParameters $queryParameters, Request $request = null) {
        if($request === null) {
            $request = app('request');
        }
        $searchQuery = $request->input('q', null);
        if($searchQuery !== null) {
            $class = new ReflectionClass($this->repository->getEntityName());
            if($class->implementsInterface(Searchable::class)) {
                $searchableFields = $class->getMethod('getSearchableFields')->invoke(null);
                $queryParameters->addClause(new SearchMultipleFieldsClause($searchableFields, $searchQuery));
            }
        }
    }

    /**
     * Returns filters for the 'list' operation
     *
     * @param Request $request
     * @return QueryParameters
     * @throws \ReflectionException
     */
    protected function getListQueryParameters(Request $request) {
        $queryParameters = QueryParameters::make()
            ->orderBy('id', QueryParameters::DESCENDING);

        $this->maybeAddSearchClause($queryParameters, $request);

        return $queryParameters;
    }

    /**
     * Shows info about an entity.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function getInfoApi($item) {
        $item = $this->repository->find((int) $item);

        return response()->json([
            'status' => Notification::SUCCESS,
            'item' => $item->toArray()
        ]);
    }

    /**
     * Creates a new Resource - returns JSON response.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postCreateApi(Request $request) {
        try {
            $item = $this->repository->make();
            $item->fromArray($request->except(['_token']));
            $this->repository->persist($item);

            return response()->json([
                'status' => Notification::SUCCESS,
                'content' => trans('oxygen/crud::messages.basic.created'),
                'item' => $item->toArray()
            ]);
        } catch(InvalidEntityException $e) {
            return response()->json([
                'status' => Notification::FAILED,
                'content' => $e->getErrors()->first()
            ]);
        }
    }

    /**
     * Updates a Resource - returns a JSON response.
     *
     * @param Request $request
     * @param mixed $item the item
     * @return JsonResponse
     * @throws Exception
     */
    public function putUpdateApi(Request $request, $item) {
        try {
            $item = $this->repository->find((int) $item);
            $item->fromArray($request->except(['_token']));
            $this->repository->persist($item);

            return response()->json([
                'content' => trans('oxygen/crud::messages.basic.updated'),
                'status' => Notification::SUCCESS,
                'item' => $item->toArray()
            ]);
        } catch(InvalidEntityException $e) {
            return response()->json([
                'content' => $e->getErrors()->first(),
                'status' => Notification::FAILED
            ]);
        }
    }

    /**
     * Deletes a Resource.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function deleteDeleteApi($item) {
        $item = $this->repository->find((int) $item);
        $this->repository->delete($item);

        return response()->json([
            'content' => trans('oxygen/crud::messages.basic.deleted'),
            'status' => Notification::SUCCESS
        ]);
    }

    public static function registerCrudRoutes(Router $router, string $resourceName) {
        $router->middleware(['web', 'oxygen.auth', '2fa.require'])->group(function() use ($router, $resourceName) {
            $router->get('/oxygen/api/' . $resourceName, static::class . '@getListApi')
                ->name($resourceName . '.getListApi')
                ->middleware("oxygen.permissions:$resourceName.getList");
            $router->post('/oxygen/api/' . $resourceName, static::class . '@postCreateApi')
                ->name($resourceName . '.postCreate')
                ->middleware("oxygen.permissions:$resourceName.postCreate");
            $router->put('/oxygen/api/' . $resourceName . '/{id}', static::class . '@putUpdateApi')
                ->name('people.putUpdate')
                ->middleware("oxygen.permissions:$resourceName.putUpdate");
            $router->delete('/oxygen/api/' . $resourceName . '/{id}', static::class . '@deleteDeleteApi')
                ->name($resourceName . '.deleteDelete')
                ->middleware("oxygen.permissions:$resourceName.deleteDelete");
            $router->get("/oxygen/api/$resourceName/{id}", static::class . '@getInfoApi')
                ->name("$resourceName.getInfoApi")
                ->middleware("oxygen.permissions:$resourceName.getInfo");
        });
    }

    public static function setupLangMappings(array $mappings) {
        Lang::when('oxygen/crud::messages', $mappings);
        Lang::when('oxygen/crud::dialogs', $mappings);
        Lang::when('oxygen/crud::ui', $mappings);
    }
}
