<?php


namespace Oxygen\Crud\Controller;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\View\View;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;

trait SoftDeleteCrudApi {

    /**
     * Filters out past versions and trashed items.
     *
     * @param Request $request
     * @return QueryParameters
     */
    protected function getListQueryParameters(Request $request) {
        $queryParameters = QueryParameters::make();
        if($request->get('trash') == 'true') {
            $queryParameters = $queryParameters->onlyTrashed();
        } else {
            $queryParameters = $queryParameters->excludeTrashed();
        }

        $queryParameters = $queryParameters
            ->orderBy('id', QueryParameters::DESCENDING);

        $this->maybeAddSearchClause($queryParameters, $request);

        return $queryParameters;
    }

    /**
     * Deletes an entity.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDeleteApi(Request $request, $item) {
        $item = $this->repository->find((int) $item);
        if($request->has('force')) {
            $this->repository->delete($item);
            return response()->json([
                'content' => __('oxygen/crud::messages.softDelete.forceDeleted'),
                'status' => Notification::SUCCESS
            ]);
        } else {
            $item->delete();
            $this->repository->persist($item);

            return response()->json([
                'content' => __('oxygen/crud::messages.basic.deleted'),
                'status' => Notification::SUCCESS
            ]);
        }
    }

    /**
     * Restores a deleted entity.
     *
     * @param mixed $item the item
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRestoreApi($item) {
        $item = $this->repository->find((int) $item);
        $item->restore();
        $this->repository->persist($item);

        return response()->json([
            'content' => __('oxygen/crud::messages.softDelete.restored'),
            'status' => Notification::SUCCESS
        ]);
    }

    public static function registerSoftDeleteRoutes(Router $router, string $resourceName) {
        $router->middleware(['web', 'oxygen.auth', '2fa.require'])->group(function() use ($router, $resourceName) {
            $router->post('/oxygen/api/' . $resourceName . '/{id}/restore', static::class . '@postRestoreApi')
                ->name($resourceName . '.postRestore')
                ->middleware("oxygen.permissions:$resourceName.postRestore");
        });
    }

}
