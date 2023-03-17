const Statuses =
{
    STATUS_OPENED: 'open',
    STATUS_MODIFICATED: 'modification',
    STATUS_CLOSED: 'close',
}

class DocumentState 
{
    _document;

    setContext(document) {
        this._document = document;
    }

    modificationDocument() {}
    openDocument() {}
    closeDocument() {}
    getState() {}
}

class ModificatedState extends DocumentState
{
    openDocument()
    {
        return 'Я не могу изменяться будучи закрытым!';
    }
    
    modificationDocument() 
    {
        return 'Я не могу быть изменён дважды!';
    }

    closeDocument() 
    {
        this._document.setState(new ClosedState());
    }

    getState()
    {
        return Statuses.STATUS_MODIFICATED;
    }
}

class OpenedState extends DocumentState
{
    openDocument()
    {
        return 'Я уже открыт!';

    }
    
    modificationDocument() 
    {
        this._document.setState(new ModificatedState());
    }

    closeDocument() 
    {
        this._document.setState(new ClosedState());
    }

    getState()
    {
        return Statuses.STATUS_OPENED;
    }
}

class ClosedState extends DocumentState
{
    openDocument()
    {
        this._document.setState(new OpenedState());
    }
    
    modificationDocument() 
    {
        return 'Я закрыт!';
    }

    closeDocument() 
    {
        return 'Я закрыт!';
    }

    getState()
    {
        return Statuses.STATUS_OPENED;
    }
}

class Document
{
    #observers;
    #state;

    constructor(state) 
    {
        this.#observers = [];
        this.setState(state);
    }

    attach(fn)
    {
        this.#observers.push(fn);
    }

    detach()
    {
        this.#observers = this.#observers.filter(subscriber => subscriber !== fn);
    }

    notify()
    {
        this.#observers.forEach(subscriber => subscriber.update(this));
    }

    openDocument()
    {
        this.#state.openDocument();
    }

    modificationDocument()
    {
        this.#state.modificationDocument();
    }

    closeDocument()
    {
        this.#state.closeDocument();
    }

    setState(state)
    {
        this.#state = state;
        this.#state.setContext(this);
        this.notify();
    }

    getState()
    {
        return this.#state.getState();
    }
}

class EnglishLangugeWriterObserver
{
    update(document)
    {
        const dictionary = new Map();
        dictionary.set(Statuses.STATUS_OPENED, 'Open');
        dictionary.set(Statuses.STATUS_MODIFICATED, 'Modification');
        dictionary.set(Statuses.STATUS_CLOSED, 'Close');

        return console.log(dictionary(document.getState()));
    }
}

class RussianLangugeWriterObserver
{
    update(document)
    {
        const dictionary = new Map();
        dictionary.set(Statuses.STATUS_OPENED, 'Открыт');
        dictionary.set(Statuses.STATUS_MODIFICATED, 'Модифицируется');
        dictionary.set(Statuses.STATUS_CLOSED, 'Закрыт');

        return console.log(dictionary(document.getState()));
    }
}

let engObserver = new EnglishLangugeWriterObserver();
let ruObserver = new RussianLangugeWriterObserver();
let document = new Document(new ClosedState());

document.attach(ruObserver);
document.attach(engObserver);
document.modificationDocument();
document.closeDocument();
document.detach(engObserver);
document.openDocument();