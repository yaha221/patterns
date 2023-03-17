<?php

namespace training\lesson_5;

/**
 * ДЗ от 08.02.2023
 */


require __DIR__ . '/../../vendor/autoload.php';


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


// ?ObjectsForm[items][0][class]=Human&ObjectsForm[items][0][sex]=man&ObjectsForm[items][0][name]=manName1&ObjectsForm[items][1][class]=Human&ObjectsForm[items][1][sex]=man&ObjectsForm[items][1][name]=manName2&ObjectsForm[items][2][class]=Human&ObjectsForm[items][2][sex]=man&ObjectsForm[items][2][name]=manName3&ObjectsForm[items][3][class]=Human&ObjectsForm[items][3][sex]=man&ObjectsForm[items][3][name]=manName4&ObjectsForm[items][4][class]=Human&ObjectsForm[items][4][sex]=women&ObjectsForm[items][4][name]=womanName1&ObjectsForm[items][5][class]=Human&ObjectsForm[items][5][sex]=women&ObjectsForm[items][5][name]=womanName2&ObjectsForm[items][6][class]=Human&ObjectsForm[items][6][sex]=women&ObjectsForm[items][6][name]=womanName3&ObjectsForm[items][7][class]=Human&ObjectsForm[items][7][sex]=women&ObjectsForm[items][7][name]=womanName4&ObjectsForm[items][8][class]=Human&ObjectsForm[items][8][sex]=women&ObjectsForm[items][8][name]=womanName5

class DTO
{
    public $roomCount;
    public $singelRoomChairCount;
    public $totalCounter;
}

/**
 * 
 */
class Human
{
    public $name;
    public $sex;

    public function __construct($name = null, $sex = 'man')
    {
        $this->name = $name;
        $this->sex = $sex;
    }
}

interface FormBuilderInterface
{
    function buildFormFromGlobals(array $request): Model;
}
interface HandlerInterface
{
    function handle(DTO $context): void;
}

interface StrategyInterface
{
    public function CalculateChairs(DTO $context): void;
}

class ChairCounterHandler implements HandlerInterface
{
    public $strategy;

    public function handle(DTO $context): void
    {
        $this->strategy->calculateChairs($context);
    }

    public function setStrategy(string $strategy, $args = null): void
    {
        $this->strategy = new $strategy($args);
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

    public function CalculateChairs(DTO $context): void
    {
        $allChairs = $context->roomCount * $context->singelRoomChairCount;

        foreach ($this->mans as $id => $man) {
            if ($man->sex === 'man') {
                continue;
            }
            unset($this->mans[$id]);
        }

        $context->totalCounter = $allChairs - sizeof($this->mans);
    }
}

class CountChairsInAllRooms implements StrategyInterface
{
    public function CalculateChairs(DTO $context): void
    {
        $context->totalCounter = $context->roomCount * $context->singelRoomChairCount;
    }
}

/**
 * Базовый класс формы
 */
abstract class Model
{
    public $items = [];

    public $errors = [];

    public function __set(string $name, $value)
    {
        if (isset($name) === true) {
            return;
        }
        throw new \InvalidArgumentException('нет такого свойства');
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }

    public function rules(): array
    {
        return [
            function(): void {
                if (isset($this->items) === true) {
                    return;
                }
                throw new \InvalidArgumentException('Параметр items не пришёл');
            },
            function(): void {
                if (is_array($this->items) === true) {
                    return;
                }
                throw new \InvalidArgumentException('Параметр items не является массивом');
            },
            function(): void {
                foreach ($this->items as $key => $item) {
                    if (is_array($item) === true) {
                        continue;
                    }
                    throw new \InvalidArgumentException('Элемент ' . $key . ' параметра items не является массивом');
                }
            },
            function(): void {
                foreach ($this->items as $key => $item) {
                    if (isset($item['class']) === true) {
                        continue;
                    }
                    throw new \InvalidArgumentException('Элемент ' . $key . ' параметра items не содержит ключ class');
                }
            }
        ];
    }

    public function validate(): void
    {
        foreach ($this->rules() as $rule) {
            try {
                $rule();
            } catch (\Exception | \InvalidArgumentException  $e) {
                $this->errors[] = $e->getMessage();
            } catch(\InvalidArgumentException $a){

            }
        }
    }
}

/**
 * Объекты формы
 */
class ObjectsForm extends Model
{
    public function getBrowser( $var = null)
    {
        $browser = "Google";
    }
}

class FormBuilder implements FormBuilderInterface
{
    private $request = [];


    private function buildModel(string $modelClass): Model
    {
        $model = new $modelClass;

        foreach ($this->request[$modelClass] as $key => $value) {

            $model->$key = $this->request[$modelClass][$key];
        }

        return $model;
    }

    private function createObjects(Model $model): void
    {
        foreach ($model as  $key => $items) {
            foreach ($items as $key => $item) {                
                if (isset($item['class']) === false) {
                    continue;
                }

                $obj = new $item['class'];

                unset($item['class']);

                foreach ($item as $property => $value) {
                    $obj->$property = $value;
                }

                $model->items[$key] = $obj;
            }
        }
    }

    public function buildFormFromGlobals(array $request): Model
    {
        $this->request = $request;

        $modelNames = array_keys($request);

        foreach ($modelNames as $name) {

            if (class_exists($name) === false) {
                continue;
            }

            $model = $this->buildModel($name);

            $model->validate();

            $this->createObjects($model);

            return $model;
        }

        throw new \InvalidArgumentException('Пришла не форма');

    }
}

// dump($_REQUEST);

$builder = new FormBuilder;
$form = $builder->buildFormFromGlobals($_REQUEST);

dump($form);

// $ChairInRoom = new DTO();
// $ChairInRoom->roomCount = 5;
// $ChairInRoom->singelRoomChairCount = 5;

// $ChairCounter = new ChairCounterHandler;

// $ChairCounter->setStrategy(CountChairsInAllRooms::class);
// $ChairCounter->handle($ChairInRoom);
// dump($ChairInRoom);

// $ChairCounter->setStrategy(CountMansChairsInAllRooms::class,$form->items);
// $ChairCounter->handle($ChairInRoom);
// dump($ChairInRoom);

die;
