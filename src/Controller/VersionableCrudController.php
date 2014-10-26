<?php

namespace Oxygen\Crud\Controller;

use View;
use Response;
use Lang;
use Input;

use Oxygen\Core\Http\Notification;

use Exception;
use Oxygen\Core\Model\Validating\InvalidModelException;

class VersionableCrudController extends SoftDeleteCrudController {

    /**
     * Shows info about a Resource.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function getInfo($item) {
        $item = $this->getItem($item);

        return View::make('oxygen/crud::versionable.show', [
            'item' => $item
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

        return View::make('oxygen/crud::versionable.update', [
            'item' => $item
        ]);
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
            $item->save(['version' => Input::get('version')]);
            $options = Input::get('version') === 'new' ? ['refresh' => true] : [];

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.updated')),
                $options
            );
        } catch(InvalidModelException $e) {
            return Response::notification(
                new Notification($e->getErrors()->first(), Notification::FAILED),
                ['input' => true]
            );
        }
    }

    /**
     * Makes a new version.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postNewVersion($item) {
        try {
            $item = $this->getItem($item);
            $item->makeNewVersion();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.madeVersion')),
                ['refresh' => true]
            );
        } catch(Exception $e) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.makeVersionFailed'), Notification::FAILED)
            );
        }
    }

    /**
     * Makes the version the head version.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postMakeHeadVersion($item) {
        try {
            $item = $this->getItem($item);

            if($item->isHead()) {
                return Response::notification(
                    new Notification(Lang::get('oxygen/crud::messages.versionable.alreadyHead'), Notification::FAILED)
                );
            }

            $item->makeHead();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.madeHead')),
                ['refresh' => true]
            );
        } catch(Exception $e) {
            dd($e);
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.makeHeadFailed'), Notification::FAILED)
            );
        }
    }

    /**
     * Clears all older versions of the item.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteVersions($item) {
        try {
            $item = $this->getItem($item);
            $item->versions()->forceDelete();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.clearedVersions')),
                ['refresh' => true]
            );
        } catch(Exception $e) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.versionable.clearVersionsFailed'), Notification::FAILED)
            );
        }
    }

    /**
     * Returns a QueryBuilder that will
     * include all special models such
     * as soft-deleted models & non-head-versions.
     *
     * @return QueryBuilder
     */

    protected function queryAll() {
        return $this->model->newQuery()->withTrashed()->withVersions();
    }

}