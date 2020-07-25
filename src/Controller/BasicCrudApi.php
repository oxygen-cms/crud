<?php


namespace Oxygen\Crud\Controller;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;

trait BasicCrudApi {

    /**
     * List all entities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getListApi(Request $request) {
        $paginator = $this->repository->paginate(self::PER_PAGE, $this->getListQueryParameters($request), null, $request->input('q', null));

        // render the list
        return response()->json([
            'items' => array_map(function($item) { return $item->toArray(); }, $paginator->items()),
            'totalItems' => $paginator->total(),
            'itemsPerPage' => $paginator->perPage(),
            'status' => Notification::SUCCESS,
        ]);
    }

    /**
     * Returns filters for the 'list' operation
     *
     * @param Request $request
     * @return QueryParameters
     */
    protected function getListQueryParameters(Request $request) {
        $queryParameters = QueryParameters::make()
            ->orderBy('id', QueryParameters::DESCENDING);
        
        return $queryParameters;
    }

    /**
     * Shows info about an entity.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function getInfoApi($item) {
        $item = $this->repository->find($item);

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
            $item = $this->repository->find($item);
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
        $item = $this->repository->find($item);
        $this->repository->delete($item);

        return response()->json([
            'content' => trans('oxygen/crud::messages.basic.deleted'),
            'status' => Notification::SUCCESS
        ]);
    }
}