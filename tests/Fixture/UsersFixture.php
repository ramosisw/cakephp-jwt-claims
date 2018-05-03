<?php

namespace RamosISW\Jwt\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    /**
     * fields property.
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer'],
        'group_id' => ['type' => 'integer', 'null' => false],
        'user_name' => ['type' => 'string', 'null' => false],
        'email' => ['type' => 'string', 'null' => false],
        'password' => ['type' => 'string', 'null' => false],
        'created' => 'datetime',
        'updated' => 'datetime',
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ];

    /**
     * records property.
     *
     * @var array
     */
    public $records = [
        [
            'group_id' => 1, 'user_name' => 'jcramos',
            'email' => 'jcramos@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
            'created' => '2018-05-03 01:18:23', 'updated' => '2018-05-03 01:20:31',
        ]
    ];
}
