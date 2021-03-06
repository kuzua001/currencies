<h1>Курсы валют</h1>
<h3>Исходная задача:</h3>

Необходимо реализовать надежную и гибкую систему работы с курсами валют со следующей логикой:

- вызывающий код может получить курсы валют из кеша 
- из базы данных
- из внешнего источника по http
 
В случае, если курса валют нет в кеше, надо проверить базу, и если там есть, положить в кэш. 
Если в базе нет - проверить внешний источник и положить и в базу, и в кэш.

Предполагается, что эта логика будет использоваться в куче разных мест.

<h3>Дополнительные технические требования:</h3>
- Нам доступны репозитории, реализующие ``CurrenciesRatesRepositoryInterface`` и инкапсулирующие в себе работу с API и базой данных.
- Эти реализации не выбрасывают исключений,
кроме метода ``setCurrencyRate`` в классе, реализующем ``CurrenciesRatesApiInterface`` - он должен выбрасывать исключение ``App\Model\Exception\MethodNotApplicable``
<br>[Что логично - посетить во внешний API курсы валют мы явно не можем.
<br>Вышеупомянутый интерфейс выделен отдельно, но ничего не привносит в базовый ``CurrenciesRatesRepositoryInterface``]
- Мы считаем, что каждый репозиторий гарантированно возвращает объект ``CurrencyRate`` если на указанный ``timestamp`` есть актуальный курс валютной пары, в противном случае - ``null``
<br><br>В принципе такое не сложно реализовать даже в случае если валюты получаются единым списком, а работа с базой происходит
целыми батчами, просто для валют нужно будет написать итератор, который будет парсить ответ on demand.

- Актуальность валютных пар должна гарантироваться каждым репозиторием, конкретно репозиторием API -  в привязке к конфигурационному параметру ``refresh_period`` из ``currencies.yaml``, например: 
```
refresh_period: 3600
cache_prefix: currencies_
supported_currencies:
  - RUB
  - JPY
  - USD
  - EUR
```
- Таким образом, что основной репозиторий API первым задает ``expiresAt`` для курса валют, это зависит как от контракта API так и от времени запроса к нему,
например, если ``https://www.cbr-xml-daily.ru/daily.xml``всегда отвечает ``200``, но он указывает в рутовой ноде атрибут ``Date="06.02.2020"``, то имеет смысл задать ``refresh_period`` < ``3600 * 24``, если 
ответы все-таки не статичны, но при этом не выставлять под конец дня больший ``expiresAt`` чем ``00:00:00`` следующего дня.
- Если валюта не поддерживается или не найдена ни в одном из репозиториев, включая API, то ожидается исключение
``CurrencyIsNotSupported`` или ``CurrencyRateIsNotFound`` соответственно.
- Идет работа только с доступным списком валют, описанным в конфигурационном файле.


<h3>ToDo list, что бы я улучшил, будь у меня больше времени</h3>
- улучшил бы работу с ttl/time, вернее унифицировал бы эту логику.
- не использовал бы в основных слоях бизнес логики получение времени через ``time()`` вообще
- написал бы тесты, которые покрывают различные варианты заполнения репозиториев, после получения рейта валют из нижележащего
- добавил бы поддержку некого ``external id`` для работы с API, поскольку код валюты вероятно может измениться (я не проверял это в соответствии с ISO)
- - добавил бы подробное логгирование 
- прогревал бы кэш и не считывал конфиг каждый раз
- улучшил бы само построение мок объектов в unit тестах, сейчас не хватило времени и они сделаны "чтобы работало"
- после некоторого количества времени жизни с этим кодом я бы дал более ёмкие и понятные названия классам и методам