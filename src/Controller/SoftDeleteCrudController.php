<?php

namespace Oxygen\Crud\Controller;

use Exception;

use Input;
use View;
use Lang;
use Response;

use Oxygen\Core\Http\Notification;

class SoftDeleteCrudController extends BasicCrudController {

    /**
     * List all entities.
     *
     * @param array $scopes
     * @return Response
     */

    public function getList($scopes = ['excludeTrashed']) {
        return parent::getList($scopes);
    }

    /**
     * List all deleted entities.
     *
     * @param array $scopes
     * @return Response
     */

    public function getTrash($scopes = ['onlyTrashed']) {
        $items = $this->repository->paginate(25, $scopes);

        return View::make('oxygen/crud::basic.list', [
            'items' => $items,
            'title' => Lang::get('oxygen/crud::ui.resource.trash'),
            'isTrash' => true
        ]);
    }

    /**
     * Deletes an entity.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteDelete($item) {
        $item = $this->getItem($item);
        $item->delete();
        $this->repository->persist($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.basic.deleted')),
            ['refresh' => true]
        );
    }

    /**
     * Restores a deleted entity.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function postRestore($item) {
        $item = $this->getItem($item);
        $item->restore();
        $this->repository->persist($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.softDelete.restored')),
            ['refresh' => true]
        );
    }



    /**
     * Deletes an entity permanently.
     *
     * @param mixed $item the item
     * @return Response
     */

    public function deleteForce($item) {
        $item = $this->getItem($item);
        $this->repository->delete($item);

        return Response::notification(
            new Notification(Lang::get('oxygen/crud::messages.softDelete.forceDeleted')),
            ['redirect' => $this->blueprint->getRouteName('getList')]
        );
    }

}