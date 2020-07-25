<?php


namespace Oxygen\Crud\Controller;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Repository\QueryParameters;

trait VersionableCrudApi {

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
            ->excludeVersions()
            ->orderBy('id', QueryParameters::DESCENDING);
        
        return $queryParameters;
    }

    /**
     * Lists all versions of an entity.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function listVersionsApi($item) {
        $item = $this->repository->find($item);
        $versions = $item->getVersions();

        return response()->json([
            'items' => array_merge([$item->getHead()->toArray()], $versions->map(function($item) { return $item->toArray(); })->toArray()),
            'status' => Notification::SUCCESS
        ]);
    }

    /**
     * Makes a new version of an entity.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function postNewVersion($item) {
        $item = $this->repository->find($item);
        $this->repository->makeNewVersion($item);

        return response()->json([
            'content' => __('oxygen/crud::messages.versionable.madeVersion'),
            'status' => Notification::SUCCESS
        ]);
    }

    /**
     * Makes the version the head version.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function postMakeHeadVersion($item) {
        $item = $this->repository->find($item);

        if($item->isHead()) {
            return response()->json([
                'content' => __('oxygen/crud::messages.versionable.alreadyHead'),
                'status' => Notification::FAILED
            ]);
        }

        $this->repository->makeHeadVersion($item);

        return response()->json([
            'content' => __('oxygen/crud::messages.versionable.madeHead'),
            'status' => Notification::SUCCESS
        ]);
    }

    /**
     * Clears all older versions of the item.
     *
     * @param mixed $item the item
     * @return JsonResponse
     */
    public function deleteVersions($item) {
        $item = $this->repository->find($item);
        $this->repository->clearVersions($item);

        return response()->json([
            'content' => __('oxygen/crud::messages.versionable.clearedVersions'),
            'status' => Notification::SUCCESS
        ]);
    }
}