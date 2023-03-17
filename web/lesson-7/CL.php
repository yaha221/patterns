<?php

/**
 * 
 */

require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Subject implements \SplSubject
{
    private \SplObjectStorage $observers;
    private string $state;

    public function __construct()
    {
        $this->observers = new SplObjectStorage;
    }

    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    public function changeState(string $state): void
    {
        $this->state = $state;
        $this->notify();
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}

class SubjectObserver implements \SplObserver
{
    private array $changeState;

    public function update(\SplSubject $subject): void
    {
        $this->changeState[] = clone $subject;
    }

    public function getChangeStatements(): array
    {
        return $this->changeState;
    }
}

$observer = new SubjectObserver();

$state = new Subject();
$state->attach($observer);

$state->changeState('Открыт');

dump($observer->getChangeStatements());

$state->changeState('Закрыт');

dump($observer->getChangeStatements());

die;