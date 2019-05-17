# ReferenceList

**ReferenceList** - структура данных [связанный список](https://ru.wikipedia.org/wiki/Связный_список) для **PHP** 7+

Доступные операции:

* вставка *(**insert**)*
* массовая вставка *(**massInsert**)*
* вставка по индексу *(**set**)*
* удаление по индексу *(**remove**)*
* получение по индексу *(**get**)*
* поиск элементов *(**indexOf**, **lastIndexOf**, **customSearch**)*
* получение списка элементов *(**list**)*
* проход по списку *(**foreach**, **where**)*
* объединение списков *(**union**)*

Хлеба и зрелищ! [Решето Эратосфена](https://ru.wikipedia.org/wiki/Решето_Эратосфена):

```php
<?php

use ReferenceList\ReferenceList;

function sieve (int $num): ReferenceList
{
    $sieve = new ReferenceList ([false, false]);

    for ($i = 2; $i <= $num; ++$i)
        $sieve[$i] = true;

    for ($i = 2; ($j = $i * $i) <= $num; ++$i)
        if ($sieve[$i] == true)
            for ($j; $j <= $num; $j += $i)
                $sieve[$j] = false;

    return $sieve;
}
```

Список простых чисел:

```php
<?php

use ReferenceList\ReferenceList;

function simples (int $num): array
{
    $simples = [];
    $sieve   = new ReferenceList ([false, false]);

    for ($i = 2; $i <= $num; ++$i)
        $sieve[$i] = true;

    for ($i = 2; ($j = $i * $i) <= $num; ++$i)
        if ($sieve[$i] == true)
            for ($j; $j <= $num; $j += $i)
                $sieve[$j] = false;

    $sieve->foreach (function ($node, $index) use (&$simples)
    {
        if ($node->data)
            $simples[] = $index;
    });

    return $simples;
}
```

Тоже самое, только количество простых чисел:

```php
<?php

use ReferenceList\ReferenceList;

function simples_count (int $num): array
{
    $sieve = new ReferenceList ([false, false]);

    for ($i = 2; $i <= $num; ++$i)
        $sieve[$i] = true;

    for ($i = 2; ($j = $i * $i) <= $num; ++$i)
        if ($sieve[$i] == true)
            for ($j; $j <= $num; $j += $i)
                $sieve[$j] = false;

    return sizeof ($sieve->where (function ($node)
    {
        return $node->data;
    })->list ());
}
```

В общем, вы поняли

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](http://vk.com/hphp_convertation)