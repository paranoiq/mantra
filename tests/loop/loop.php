<?php

use Nette\Environment;

require_once '../bootstrap.php';

$db = Environment::getService('Phongo\IConnection');
$db->connect();
$db->setSafeMode();

set_time_limit(0);

$db->selectCollection('abcd', 'test');

$n = 1000000;
while ($n > 0) {
    $result = $db->find(array());
    $result->fetchAll();
    $n--;
    echo $n . "<br>";
}
