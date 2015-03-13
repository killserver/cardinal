# Cardinal Engine(Core) [B]

Что нужно для запуска сайта на этом движке?

По-этапно:

 * Переименовать файл config.default.php на config.php в папке core/media/
 * Настроить config.php
 * В той же папке переименовать файл db.default.php в db.php
 * Настроить db.php
 
Далее идёт краткий обзор функционала с примерами его работы предоставляемый Cardinal Engine. Однако если Вы человек разбирающийся - Вы сможете легко и не принуждённо рассмотреть *ядро* самостоятельно для получения максимально возможного функционала.
 
 * ##Что касается работы с базой данных.
#### Простое получение данных(одна запись):
```php
$row = db::doquery("SELECT * FROM news WHERE id = ".intval($_GET['id']));
```
#### Простое получение данных(все записи в таблице):
```php
$rows = db::doquery("SELECT * FROM news ORDER BY id DESC", true);
while($row = db::fetch_assoc()) {
...
}
```

* ##Что касается работы с шаблонизатором.
#### Добавление данных в шаблонизатор(одна запись):
```php
templates::assing_var("is_view", "1");
```
#### Добавление данных в шаблонизатор(множество записей):
```php
templates::assing_vars(array(
"is_view1" => "1",
"is_view2" => "2",
));
```
#### Добавление данных в шаблонизатор(множество записей циклом):
```php
for($i=0;$i<10;$i++) {
  templates::assing_vars(array(
  "is_view1" => "1",
  "is_view2" => "2",
  ), "news", "news".$i);
}
```

* ##Что касается, непосредственно работы с шаблоном.
#### Условия:
```php
[if 1==1]true[/if]
```
```php
[if 1==2]true[else]false[/if]
```
```php
[if 1==1]true[/if 1==1]
```
```php
[if 1==2]true[else 1==2]false[/if 1==2]
```
Условия могут быть вложенны только при условии их чёткого заверешния
```php
[if 1==2]
	true
[else 1==2]
	[if 1==1]true[else 1==1]false[/if 1==1]
[/if 1==2]
```

* #### Циклы:
```php
[foreach block=news]
 <h1>{news.is_view1}</h1>
 {news.is_view2}
[/foreach]
```
```php
[foreach block=news]
 <h1>{news.is_view1}</h1>
 {news.is_view2}
[/foreach news]
```
##### !Внимание! Внутри цикла применяется отличная от обычных условий конструкция со схожей логикой:
```php
[foreach block=news]
[foreachif {news.is_view1}==1]
 <h1>{news.is_view1}</h1>[/foreachif]
 {news.is_view2}
[/foreach]
```

#Если у Вас есть какие-то вопросы - направляем их по-адресу: [email]
А так-же - следите за обновлениями.


[email]:mailto:killer-server@mail.ru
