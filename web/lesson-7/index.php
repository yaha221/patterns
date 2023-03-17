<?php

/**
 * 
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
        return Statuses::STATUS_MODIFICATED;
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
        return Statuses::STATUS_OPENED;
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
        return Statuses::STATUS_CLOSED;
    }
}

/**
 * Класс документа
 */
class Document implements \SplSubject
{
    private \SplObjectStorage $observers;

    /** @var DocumentState */
    private $state;

    public function __construct(DocumentState $state)
    {
        $this->observers = new \SplObjectStorage;
        $this->setState($state);
    }

    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
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
    public function setState(DocumentState $state): void
    {
        $this->state = $state;
        $this->state->setContext($this);
        $this->notify();
    }

    public function getState()
    {
        return $this->state->getState();
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}

class Statuses 
{
    const STATUS_OPENED = 'open';
    const STATUS_MODIFICATED = 'modification';
    const STATUS_CLOSED = 'close';
}

class RussianLangugeWriterObserver implements \SplObserver
{
    public function update(\SplSubject $document): void
    {
        $dictionary =[
            Statuses::STATUS_OPENED => 'Открыто',
            Statuses::STATUS_MODIFICATED => 'Модифицировано',
            Statuses::STATUS_CLOSED => 'Закрыто',
        ];

        echo $dictionary[$document->getState()] . '<br>';
    }
}

class EnglishLangugeWriterObserver implements \SplObserver
{
    public function update(\SplSubject $document): void
    {
        $dictionary =[
            Statuses::STATUS_OPENED => 'Open',
            Statuses::STATUS_MODIFICATED => 'Modificate',
            Statuses::STATUS_CLOSED => 'Close',
        ];

        echo $dictionary[$document->getState()] . '<br>';
    }
}

$observer = new EnglishLangugeWriterObserver();

$ruObserver = new RussianLangugeWriterObserver();

$document = new Document(new OpenedState());
$document->attach($ruObserver);
$document->attach($observer);

// dump($document->getState());
// dump($document->openDocument());

$document->modificationDocument();


// dump($document->getState());
// dump($document->modificationDocument());

$document->closeDocument();

$document->detach($observer);

$document->openDocument();


// dump($document->getState());
// dump($document->modificationDocument());
// dump($document->closeDocument());

die;
