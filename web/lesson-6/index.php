<?php

/**
 * Паттерн состояние
 */
// use training\lesson_5\Human;

/**
 * Самостоятельно разобраться и придумать собственную реализацию шаблона состояние
 * 
 * Идея: Документ и его состояния
 * Варианты переходов между состояниями(открыто, редактируется, закрыто):
 * открыто -> редактируется,
 * открыто -> закрыто,
 * редактируется -> закрыто,
 * закрыто -> открыто,
 */

require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/**
 * Абстрактный класс состояния документа состоящий из получаемого документа
 * Конструктора принимающего объект документа и набора состояний
 */
abstract class DocumentState
{
    /** @var Document */
    protected $document;

    /**
     *  Получение контекста
     * 
     * @var Document
     * @return void
     */
    public function setContext(Document $document)
    {
        $this->document = $document;
    }

    /** Изменение объекта */
    abstract function modificationDocument();

    /** Открыти объекта */
    abstract function openDocument();

    /** Закрытие объекта */
    abstract function closeDocument();

    /** Получение состояния */
    abstract function getState();
}

/**
 * Состояние изменяемого объекта
 */
class ModificatedState extends DocumentState
{
    /**
     * Находясь в состоянии модификации, объект не может быть ещё больше модифицирован
     */
    public function modificationDocument()
    {
        return 'Я не могу быть изменён дважды, мужик!';
    }

    public function closeDocument()
    {
        $this->document->setState(new ClosedState());
    }

    /**
     * Находясь в состоянии модификации, объект не может быть закрытым
     */
    public function openDocument()
    {
        return 'Я не могу изменяться будучи закрытым, мужик!';
    }

    public function getState()
    {
        return 'Документ изменён и сохраняется автоматически';
    }
}

/**
 * Состояние открытого объекта
 */
class OpenedState extends DocumentState
{
    public function modificationDocument()
    {
        $this->document->setState(new ModificatedState());
    }

    public function closeDocument()
    {
        $this->document->setState(new ClosedState());
    }

    /**
     * Находясь в открытом состоянии, объект не может быть ещё раз открыт
     */
    public function openDocument()
    {
        return 'Я уже открыт, что ты хочешь от меня мужик?';
    }

    public function getState()
    {
        return 'Документ открыт и готов к чтению';
    }
}

/**
 * Состояние закрытого объекта
 */
class ClosedState extends DocumentState
{

    /**
     * Находясь в закрытом состоянии, объект не может быть модифицировна
     */
    public function modificationDocument()
    {
        return 'Я закрыт, как ты вообще тут оказался?';
    }

    /**
     * Находясь в закрытом состоянии, объект не может быть закрыт
     */
    public function closeDocument()
    {
        return 'Я закрыт, как ты вообще тут оказался?';
    }

    public function openDocument()
    {
        $this->document->setState(new OpenedState());
    }

    public function getState()
    {
        return 'Документ закрыт';
    }
}

/**
 * Класс документа
 */
class Document 
{
    /** @var DocumentState */
    private $state;

    public function __construct(DocumentState $state)
    {
        $this->setState($state);
    }

    public function modificationDocument()
    {
        return $this->state->modificationDocument();
    }

    public function closeDocument()
    {
        return $this->state->closeDocument();
    }

    public function openDocument()
    {
        return $this->state->openDocument();
    }

    /**
     * Установка состояния
     */
    public function setState(DocumentState $state)
    {
        $this->state = $state;
        $this->state->setContext($this);
    }

    public function getState()
    {
        return $this->state->getState();
    }
}

$document = new Document(new OpenedState());

dump($document->getState());
dump($document->openDocument());

$document->modificationDocument();

dump($document->getState());
dump($document->modificationDocument());

$document->closeDocument();

dump($document->getState());
dump($document->modificationDocument());
dump($document->closeDocument());

die;
