Чтобы запустить выполните следующие команды:
- Скачать пакеты запустив команду в консоли: `composer update`
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
    [correlation_id] => e8a1ace08d3da94a9249907d9b585145
    [message_id] => 11995
    [app_id] => 
    [span_id] => 1
)

Array
(
    [event_id] => 11995
    [name] => docx
    [age] => 25
)

```


