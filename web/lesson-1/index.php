<?php
namespace training\lesson_1;
/**
 * Передача значений по ссылке и по значению
 */

require __DIR__ . '/../../vendor/autoload.php';

$a = [1, 2, 3, 4, 5];
dump($a);

$b = $a;
dump($b);

$b[] = 1;

dump($b);
dump($a);

$c = &$a;
$c[] = 7;
dump($a);
dump($c);

die;