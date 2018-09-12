Чтобы запустить выполните следующие команды:
- Скачать пакет через composer:
```bash
composer require chocofamilyme/pubsub
```
- В консоли запустить паблишера: `php pub.php`.
Ожидаемый результат:
```bash
OK
```
- В другой консоли запустить подписчика: `php sub.php`. 
Ожидаемый результат:
```bash
Array
(
    [delivery_mode] => 2
    [correlation_id] => da2b9f5443a49539c74d54931aef6741
    [message_id] => 11995
    [app_id] => service.example.com
    [span_id] => 1
)

Array
(
    [event_id] => 11995
    [name] => docx
    [age] => 25
)

```


