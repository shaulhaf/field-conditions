<?php

namespace Shaulhaf\FieldConditions;

use Laravel\Nova\Fields\Field;

class FieldConditions extends Field
{
    public $component = 'field-conditions';

    public function __construct($options)
    {
        $this->withMeta(compact('options'));
    }
}
