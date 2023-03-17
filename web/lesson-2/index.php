<?php

namespace training\lesson_2;

/**
 * Передача объекта по ссылке и по значению
 */

require __DIR__ . '/../../vendor/autoload.php';
/*
class A
{
    public $id;
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

$a = new A(5);
$b = clone $a;
dump($a);
$b->id = 2;
dump($a, $b);

die;

/**
 * Анти-паттерн Singleton и области видимости функции
 */
/*
$a = 5;

function create(...$a)
{
    var_dump($a);
}

create($a);
*/
class Singleton
{
    public $id = 5;
    public static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if(self::$instance === null){
            self::$instance = new self();
        }

        return self::$instance;
    }
}


$first = Singleton::getInstance();
$second = Singleton::getInstance();

echo $first->id . PHP_EOL;
$first->id = 10;
echo $second->id;

dump($first, $second);

die;