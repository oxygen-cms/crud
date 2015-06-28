<?php

namespace Oxygen\Crud\Fields;

use Oxygen\Core\Form\FieldMetadata;

trait PublishedField {

    public function getPublishedField() {
        return new FieldMetadata('stage', 'select', true);
    }

}