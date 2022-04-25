<?php

ini_set('xdebug.var_display_max_depth', 10);

require 'vendor/autoload.php';

$array = [
    'table1' => [
        ['id' => ['int'], 'name' => ['varchar', 32], 'age' => ['int']],
        [1, 'John', 20],
        [2, 'Jane', 21],
        [3, 'Jack', 20],
        [4, 'Jake', 19],
    ]
];

$db = new Leven\DBA\Mock\MockAdapter(
    fn() => json_decode(file_get_contents('mock.json'), true),
    fn($array) => file_put_contents('mock.json', json_encode($array, JSON_PRETTY_PRINT))
);


//$r = $db->insert('table1', ['id' => 10, 'name' => 'Leon', 'age' => 22]);

/*$r = $db->select('table1')
    ->where('age', '!=', '20')
    ->orderAsc('age')
    ->execute()
;
print_r($r);*/

/*$r = $db->update('table1')
    ->set(['age' => 99])
    ->where('id', '==', 3)
    ->execute()
;
print_r($r);*/

$db->txnBegin();
$db->delete('table1')->execute();
print_r($db->select('table1')->execute());
$db->txnCommit();
print_r($db->select('table1')->execute());
