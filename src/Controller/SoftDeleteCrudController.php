<?php

namespace Oxygen\Crud\Controller;

use Illuminate\Http\Response;
use Illuminate\View\View;
use Oxygen\Core\Http\Notification;
use Oxygen\Data\Exception\InvalidEntityException;
use Oxygen\Data\Repository\QueryParameters;

class SoftDeleteCrudController extends BasicCrudController {

    /**
     * Deletes an entity.
     *
     * @param mixed $item the item
     * @return Response
     * @throws InvalidEntityException
     */
    public function deleteDelete($item) {
        $item = $this->getItem($item);
        $item->delete();
        $this->repository->persist($item);

        return notify(
            new Notification(__('oxygen/crud::messages.basic.deleted')),
            ['refresh' => true]
        );
    }

    /**
     * Restores a deleted entity.
     *
     * @param mixed $item the item
     * @throws InvalidEntityException
     * @return Response
     */
    public function postRestore($item) {
        $item = $this->getItem($item);
        $item->restore();
        $this->repository->persist($item);

        return notify(
            new Notification(__('oxygen/crud::messages.softDelete.restored')),
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

        return notify(
            new Notification(__('oxygen/crud::messages.softDelete.forceDeleted')),
            ['redirect' => $this->blueprint->getRouteName('getList')]
        );
    }

}
