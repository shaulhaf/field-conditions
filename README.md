# field-conditions
A Laravel Nova field

            FieldConditions::make([
              [
                  //'field' => ['name', 'color'],
                  'field' => 'name',
                  'conditions' => [
                      [
                          ['field' => 'select', 'operator' => '=', 'value' => 'yes',],
                          ['field' => 'select2', 'operator' => '==', 'value' => 'yes',],
                      ],
                      [
                          ['field' => 'select3', 'operator' => 'includes', 'value' => ['yes', 'no'],],
                      ],
                      [
                          ['field' => 'select3', 'operator' => 'excludes', 'value' => ['yes', 'no'],],
                      ],
                   ]
                ],
            ]),
