<?php

namespace training\lesson_3;


/**
 * Шаблон команда
 */

require __DIR__ . '/../../vendor/autoload.php';

class DTO
{
    public $num1;
    public $num2;
    public $total;
}

interface HandlerRunnerInterface
{
    function pushHandler(string $className): void;

    function run(DTO $context): void;
}

class Invoker implements HandlerRunnerInterface 
{
    private $commands = [];

    public function pushHandler(string $className): void
    {
        $this->commands[] = new $className;
    }

    public function run(DTO $context): void
    {
        foreach ($this->commands as $command) {
            $command->handle($context);
            dump($context->total);
        }
    }
}

interface HandlerInterface
{
    function handle(DTO $context): void;
}

class CreateDataHandler implements HandlerInterface 
{
    public function handle(DTO $context) : void
    {
        $context->num1 = 5;
        $context->num2 = 3;
    }
}

class SumHandler implements HandlerInterface 
{
    public function handle(DTO $context) : void
    {
        $context->total = ($context->num1 + $context->num2);
    }
}

class DivisionHandler implements HandlerInterface 
{
    public function handle(DTO $context) : void
    {
        $context->total = ($context->num1 / $context->num2);
    }
}

class MultiplayHandler implements HandlerInterface 
{
    public function handle(DTO $context) : void
    {
        $context->total = ($context->num1 * $context->num2);
    }
}

$dto = new DTO;
$invoker = new Invoker;
$invoker->pushHandler(CreateDataHandler::class);
$invoker->pushHandler(SumHandler::class);
$invoker->pushHandler(DivisionHandler::class);
$invoker->pushHandler(MultiplayHandler::class);
$invoker->run($dto);
dump($dto);

die;