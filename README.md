# Библиотека для реализации паттерна pub/sub для фреймворка Phalcon

Библиотека реализует событийную архитектуру приложений (Event-Driven Architecture). Работает с фреймворком Phalcon 3.x, но при желании можно легко адаптировать под другие фреймворки.  

### Возможности
- Транзакционное сохранение моделей ORM и публикация события
- Публикация событий без транзакции
- Подписка на события 
- Повторная отправка события в ту же очередь при необходимости
- Сохранение в общую очередь всех не обработанных и истекших сообщений. Из этой очереди потом можно сохранить куда-то в БД и обработать индивидуально

### Требования
- Phalcon 3.x+
- PHP 7.0+

### Настройка

На данный момент библиотека работает только с RabbitMQ, при желаении можно добавить другие. 

#### Настройка конфигов
```php
...

'eventsource' => [
    'default' => env('MESSAGE_BROKER', 'rabbitmq'),
    
    'drivers' => [
        'rabbitmq' => [
            'adapter'    => 'RabbitMQ',
            'host'     => env('EVENTSOURCE_HOST', 'eventsource'),
            'port'     => env('EVENTSOURCE_PORT', '5672'),
            'user'     => env('EVENTSOURCE_USER', 'guest'),
            'password' => env('EVENTSOURCE_PASSWORD', 'guest'),
        ],
    ],
]

...
```

#### Добавляем брокер в DI контейнер

```php
$di = \Phalcon\Di::getDefault();
$config      = $di->get('config')->eventsource;
$config      = $config->drivers[$config->default];

$serviceName = $di->get('config')->domain;
$cache       = $di->get('cache');

$di->setShared('eventsource',
    function () use ($config, $serviceName, $cache) {
        $adapter = $config->adapter;
        $config  = array_merge($config->toArray(), ['app_id' => $serviceName]);
        $class   = 'Chocofamily\PubSub\Provider\\'.$adapter;

        $repeater = new Repeater($cache);
        return $class::getInstance($config, $repeater);
    }
);
```

Здесь `$cache` объект реализующий интерефейс `Phalcon\Cache\BackendInterface`. Кэш используется для подсчета количества повторной обработки определенного сообщения.

### Использование

#### Публикация
Публиковать сообщения можно используя класс `Chocofamily\PubSub\Publisher`. Минимальный рабочий пример:
````php
$publisher = new Publisher($di->getShared('eventsource'));

$payload = [
	'event_id' => 11995,
	'name' => 'docx',
	'age' => 25
];

$routeKey = 'order.created';

$publisher->send($payload, $routeKey);
````
Для RabbitMQ переменная `$routeKey` должна состоять минимум из двух частей разделенных точкой `.`. Пример `order.created`. Имя Exchange будет содержать первый блок, т.е. `order`. После этого если зайдете в админку rabbitmq должен создаться exchange с именем `order`.

#### Подписка на событие
Для подписки на события используется класс `Chocofamily\PubSub\Subscriber`. Минимальный рабочий пример:

````php
$params = [
    'queue_name' => 'restapi_orderx',
];

$taskName = 'your_task_name';

$subscriber = new Subscriber($di->getShared('eventsource'), 'order.created.*', $params, $taskName);

$subscriber->subscribe(function ($headers, $body) {
    echo print_r($headers, 1). PHP_EOL;
    echo print_r($body, 1). PHP_EOL;
});
````

Чтобы обратно отправить сообщение в очередь необходимо в кэлбэк функции кинуть исключение `Chocofamily\PubSub\Exceptions\RetryException`. Сообщение может максимум 5 раз обработаться повторно, после этого он попадает в очередь мертвых сообщений (exchange = DLX). 


#### Публикация используя транзакции БД
Этот способ необходим для атомарности сохранения сущности в БД и публикования события. Следующая картинка хорошо иллюстрирует как это работает:
![alt text](https://image.ibb.co/nvznx9/richardson_microservices_part5_local_transaction_e1449165852332.jpg)

Для этого необходимо создать таблицу events:

````sql
create table events
(
	id serial not null
		constraint events_pkey
			primary key,
	type smallint not null,
	payload json not null,
	status smallint not null,
	model_id integer not null,
	created_at timestamp default now() not null,
	updated_at timestamp
);
````


Пример использования:
````php
use Chocofamily\PubSub\Services\EventPrepare;

...

$order = new Order([
    'user_id' => 11166541,
    'status'  => 0,
    'total'   => 5852,
]);

$eventSource = $di->get('eventsource');

$event = new EventPrepare($order, new OrderSerialize(['name' => 'docx']), 1);
$event->up($eventSource, 'order.created.-5');
	
````

Метод `up` работает так
- db transaction start
- order->save();
- eventModel->save()
- db transaction commit
- event publish








@todo
- Написать интерфейс для транзакций и убрать зависимость от фреймворка
- Написать интерфейс для моделей таблицы events 
