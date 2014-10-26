<?php

namespace Oxygen\Crud\Controller;

use Exception;

use View;
use Lang;
use Response;

use Oxygen\Core\Http\Notification;

class SoftDeleteCrudController extends BasicCrudController {

    /**
     * List all deleted Resources.
     *
     * @return Response
     */

    public function getTrash() {
        $items = $this->model->onlyTrashed()->paginate(25);

        return View::make('oxygen/crud::basic.list', [
            'items' => $items,
            'isTrash' => true
        ]);
    }

    /**
     * Restores a deleted Resource.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postRestore($item) {
        try {
            $item = $this->getItem($item);
            $item->restore();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.softDelete.restored')),
                ['refresh' => true]
            );
        } catch(Exception $e) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.softDelete.restoreFailed'), Notification::FAILED)
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
                ['refresh' => true]
            );
        } catch(Exception $e) {
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.basic.deleteFailed'))
            );
        }
    }

    /**
     * Deletes a Resource permanently.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteForce($item) {
        try {
            $item = $this->getItem($item);
            $item->forceDelete();

            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.softDelete.forceDeleted')),
                ['redirect' => $this->blueprint->getRouteName('getList')]
            );
        } catch(Exception $e) {
            dd($e);
            return Response::notification(
                new Notification(Lang::get('oxygen/crud::messages.softDelete.forceDeleteFailed'), Notification::FAILED)
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
        return $this->model->newQuery()->withTrashed();
    }

}