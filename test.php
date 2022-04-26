<?php

use Leven\DBA\Mock\Structure\Column;
use Leven\DBA\Mock\Structure\ColumnType;

ini_set('xdebug.var_display_max_depth', 10);

require 'vendor/autoload.php';

$array = [
    'table1' => [
        'autoIncrement' => 4,
        [new Column('id', ColumnType::INT, nullable: false, unique: true, autoIncrement: true), 'name' => ['varchar', 32], 'age' => ['int']],
        [1, 'John', 20],
        [2, 'Jane', 21],
        [3, 'Jack', 20],
        [4, 'Jake', 19],
    ]
];

$db = new Leven\DBA\Mock\MockAdapter(
    $array,
    //fn() => unserialize(file_get_contents('mock.json')),
    fn($db) => file_put_contents('mock.json', serialize($db)),
);


$r = $db->insert('table1', ['name' => 'Leon', 'age' => 22]);
print_r($r);
//$r = $db->insert('table1', ['name' => 'Tin', 'age' => 15]);
//print_r($r);

/*$r = $db->update('table1')
    ->set('id', 5)
    ->set('name', 'Tester')
    ->where('name', '==', 'Leon')
    ->execute();*/


$r = $db->select('table1')
    //->where('age', '!=', '20')
    ->orderAsc('age')
    ->execute()
;
print_r($r);

/*$r = $db->update('table1')
    ->set(['age' => 99])
    ->where('id', '==', 3)
    ->execute()
;
print_r($r);*/

/*$db->txnBegin();
$db->delete('table1')->execute();
print_r($db->select('table1')->execute());
$db->txnCommit();
print_r($db->select('table1')->execute());
*/
