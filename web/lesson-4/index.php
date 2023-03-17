<?php

namespace training\lesson_4;

/**
 * Стратегия и её применение
 */

require __DIR__ . '/../../vendor/autoload.php';

class DTO
{
    public $roomCount;
    public $singelRoomChairCount;
    public $totalCounter;
}

class Human
{
    public $name;
    public $sex = 'man';
}

interface HandlerInterface
{
    function handle(DTO $context): void;
}

interface StrategyInterface
{
    public function CalculateChairs(DTO $context) : void;
}

class CountChairsInAllRooms implements StrategyInterface
{
    public function CalculateChairs(DTO $context) : void
    {
        $context->totalCounter = $context->roomCount * $context->singelRoomChairCount;
    }
}

class CountMansChairsInAllRooms implements StrategyInterface
{
    private $mans = [];

    public function __construct(...$args)
    {
        foreach ($args as  $people) {
            foreach ($people as $human) {
                if ($human instanceof Human === false) {
                    continue;
                }
                $this->mans[] = $human;
            }
        }
    }

    public function CalculateChairs(DTO $context) : void
    {
        $allChairs = $context->roomCount * $context->singelRoomChairCount;

        foreach ($this->mans as $id => $man) {
            if($man->sex === 'man') {
                continue;
            }
            unset($this->mans[$id]);
        }

        $context->totalCounter = $allChairs - sizeof($this->mans);
    }
}

class CountFreeChairsInAllRooms implements StrategyInterface
{
    protected $people = [];

    public function __construct(...$args)
    {
        foreach ($args as  $people) {
            foreach ($people as  $human) {
                if ($human instanceof Human === false) {
                    continue;
                }
                $this->people[] = $human;
            }
        }
    }

    public function CalculateChairs(DTO $context) : void
    {
        $allChairs = $context->roomCount * $context->singelRoomChairCount;
        $context->totalCounter = $allChairs - sizeof($this->people);
    }
}

class ChairCounterHandler implements HandlerInterface 
{
    public $strategy;

    public function handle(DTO $context) : void
    {
        $this->strategy->calculateChairs($context);
    }

    public function setStrategy(string $strategy, $args = null):void
    {
        $this->strategy = new $strategy($args);
    }
}

$ChairInRoom = new DTO();
$ChairInRoom->roomCount = 10;
$ChairInRoom->singelRoomChairCount = 15;

$ChairCounter = new ChairCounterHandler;
$ChairCounter->setStrategy(CountChairsInAllRooms::class);
$ChairCounter->handle($ChairInRoom);
dump($ChairInRoom);

$people = [];
for ($i=0; $i < 10; $i++) { 
    $middelPeople = new Human();

    $i%3 === 0 ? $middelPeople->name = 'Roberto' : $middelPeople->sex = 'woman';
    
    $middelPeople->sex === 'woman' ? $middelPeople->name = 'Henry' : '';

    $people[] = $middelPeople;
}

$ChairCounter->setStrategy(CountFreeChairsInAllRooms::class, $people);
$ChairCounter->handle($ChairInRoom);
dump($ChairInRoom);

$ChairCounter->setStrategy(CountMansChairsInAllRooms::class, $people);
$ChairCounter->handle($ChairInRoom);
dump($ChairInRoom);


die;