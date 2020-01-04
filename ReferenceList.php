<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     ReferenceList
 * @copyright   2019 - 2020 Podvirnyy Nikita (Observer KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @author      Podvirnyy Nikita (Observer KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

/**
 * @example
 * 
 * <?php
 * 
 * require 'ReferenceList.php';
 * 
 * use ReferenceList\ReferenceList;
 * 
 * $list = new ReferenceList;
 * 
 * $list[0] = 'kek';
 * $list[1] = 'lol';
 * $list[2] = 'arbidol';
 * 
 * $list->insert ('ololo')
 *      ->union (new ReferenceList (['a', 'b', 'c']));
 * 
 * // Выведет ['arbidol', 'a']
 * print_r ($list->where (function ($node)
 * {
 *     return $node->data[0] == 'a';
 * })->list ());
 * 
 */

namespace ReferenceList;

/**
 * Нода связанного списка
 */
class Node
{
    public $data;
    protected $nextNode = null;

    public function __construct ($data)
    {
        $this->data = $data;
    }

    /**
     * Получение следующей ноды
     * 
     * @return Node|null
     */
    public function getNext (): ?Node
    {
        return $this->nextNode;
    }

    /**
     * Вставка следующей ноды
     * 
     * @param Node $node - нода для вставки
     * 
     * @return Node - возвращает саму себя
     */
    public function insertNext (Node $node): Node
    {
        if ($this->nextNode !== null)
            $node = $node->insertLast ($this->nextNode);

        $this->nextNode = $node;

        return $this;
    }

    /**
     * Удаление следующей ноды
     * 
     * @return Node - возвращает саму себя
     */
    public function removeNext (): Node
    {
        if ($this->nextNode !== null)
        {
            $node = $this->nextNode->getNext ();
            $this->nextNode = null;

            if ($node !== null)
                $this->insertNext ($node);
        }

        return $this;
    }

    /**
     * Вставка последней ноды
     * 
     * @param Node $node - нода для вставки
     * 
     * @return Node - возвращает саму себя
     */
    public function insertLast (Node $node): Node
    {
        if ($this->nextNode === null)
            $this->nextNode = $node;

        else $this->nextNode->insertLast ($node);

        return $this;
    }

    /**
     * Удаление последней ноды
     * 
     * @return Node - возвращает саму себя
     */
    public function removeLast (): Node
    {
        if ($this->nextNode->getNext () === null)
            $this->nextNode = null;

        else $this->nextNode->removeLast ();

        return $this;
    }

    /**
     * Вставка ноды через $index от текущей
     * 
     * @param int $index - позиция вставляемой ноды
     * @param Node $node - нода для вставки
     * 
     * @return Node - возвращает саму себя или новую ноду-родитель (если $index = 0)
     */
    public function insertAt (int $index, Node $node): Node
    {
        if ($index == 0)
            return $node->insertNext ($this);

        elseif ($index == 1)
            $this->insertNext ($node);

        elseif ($index > 1)
            $this->insertAt ($index - 1, $node);

        else throw new \Exception ('$index should be upper than 0 or equal him');

        return $this;
    }

    /**
     * Удаление ноды через $index от текущей
     * 
     * @param int $index - позиция удаляемой ноды
     * 
     * @return Node - возвращает саму себя или новую ноду-родитель (если $index = 0)
     */
    public function removeAt (int $index): Node
    {
        if ($index == 0)
            return $this->getNext ();

        elseif ($index == 1)
            $this->removeNext ();

        elseif ($index > 1)
            $this->removeAt ($index - 1);

        else throw new \Exception ('$index should be upper than 0 or equal him');

        return $this;
    }
}

/**
 * Интерфейс для работы со связанным списоком
 */
class ReferenceList implements \ArrayAccess
{
    public $node = null;
    protected $count = 0;

    /**
     * Конструктор
     * 
     * [@param array $items = []] - список значений для вставки
     */
    public function __construct (array $items = [])
    {
        $this->massInsert ($items);

        $this->count = sizeof ($items);
    }

    /**
     * Вставка значения
     * 
     * @param mixed $data - значение для вставки
     * 
     * @return ReferenceList - возвращает сам себя
     */
    public function insert ($data): ReferenceList
    {
        $this->node = $this->node !== null ?
            $this->node->insertLast (new Node ($data)) :
            new Node ($data);

        ++$this->count;

        return $this;
    }

    /**
     * Массовая вставка значений
     * 
     * @param array $items - список значений для вставки
     * 
     * @return ReferenceList - возвращает сам себя
     */
    public function massInsert (array $items): ReferenceList
    {
        foreach ($items as $item)
            $this->insert ($item);

        return $this;
    }

    /**
     * Установка значения
     * 
     * @param int $index  - индекс для установки значения
     * @param mixed $data - значение для установки
     * 
     * @return ReferenceList - возвращает сам себя
     * 
     * @throws \Exception - выбрасывает исключение при неверном параметре $index
     */
    public function set (int $index, $data): ReferenceList
    {
        if ($index < 0 || $index > $this->count)
            throw new \Exception ('$index should be upper than 0 or equal him and be smaller than '. $this->count);

        if ($index == 0)
        {
            $node = new Node ($data);

            if ($this->node === null)
                ++$this->count;

            else $node->insertNext ($this->node->getNext ());

            $this->node = $node;
        }

        elseif ($this->node !== null)
        {
            $node = $this->node;

            while (--$index > 0)
                $node = $node->getNext ();

            if ($node->getNext () === null)
                ++$this->count;

            $node->removeNext ()->insertNext (new Node ($data));
        }

        return $this;
    }

    /**
     * Удаление значения
     * 
     * @param int $index - индекс для удаления
     * 
     * @return ReferenceList - возвращает сам себя
     * 
     * @throws \Exception - выбрасывает исключение при неверном параметре $index
     */
    public function remove (int $index): ReferenceList
    {
        if ($index < 0 || $index > $this->count)
            throw new \Exception ('$index should be upper than 0 or equal him and be smaller than '. $this->count);

        if ($this->node !== null)
        {
            $this->node = $this->node->removeAt ($index);

            --$this->count;
        }

        return $this;
    }

    /**
     * Получение значения
     * 
     * @param int $index - индекс для получения
     * 
     * @return ReferenceList - возвращает сам себя
     * 
     * @throws \Exception - выбрасывает исключение при неверном параметре $index или если ноды не существует
     */
    public function get (int $index)
    {
        if ($index >= $this->count && $index < 0 || !$this->node)
            throw new \Exception ('Node with index $index not exists');
        
        $node = $this->node;

        while ($index-- > 0)
            $node = $node->getNext ();

        return $node->data;
    }

    /**
     * Получение индекса ноды
     * 
     * @param mixed $data - значение ноды
     * 
     * @return int|null - возвращает индекс ноды или null, если её не существует
     */
    public function indexOf ($data): ?int
    {
        $i = 0;
        $node = $this->node;

        while ($node !== null)
        {
            if ($node->data == $data)
                return $i;

            $node = $node->getNext ();
            ++$i;
        }

        return null;
    }

    /**
     * Получение индекса последней ноды
     * 
     * @param mixed $data - значение ноды
     * 
     * @return int|null - возвращает индекс последней ноды или null, если её не существует
     */
    public function lastIndexOf ($data): ?int
    {
        $i     = 0;
        $lastI = null;
        $node  = $this->node;

        while ($node !== null)
        {
            if ($node->data == $data)
                $lastI = $i;

            $node = $node->getNext ();
            ++$i;
        }

        return $lastI;
    }

    /**
     * Получение ноды по пользовательскому компаратору
     * 
     * @param \Closure $comparator - компаратор для поиска (аргумент - Node)
     * 
     * @return Node|null - возвращает найденную ноду или null, если нода не найдена
     */
    public function customSearch (\Closure $comparator): ?Node
    {
        $node = $this->node;

        while ($node !== null)
        {
            if ($comparator ($node))
                return $node;

            $node = $node->getNext ();
        }

        return null;
    }

    /**
     * Получение списка значений
     * 
     * @return array - возвращает список значений списка
     */
    public function list (): array
    {
        $list = [];
        $node = $this->node;

        while ($node !== null)
        {
            $list[] = $node->data;

            $node = $node->getNext ();
        }

        return $list;
    }

    /**
     * Проход по списку
     * 
     * @param \Closure $callable - функция для прохода по списку (аргументы - Node, int - индекс)
     * 
     * @return ReferenceList - возвращает сам себя
     */
    public function foreach (\Closure $callable): ReferenceList
    {
        $i = 0;
        $node = $this->node;

        while ($node !== null)
        {
            $callable ($node, $i++);

            $node = $node->getNext ();
        }

        return $this;
    }

    /**
     * Получение нового списка по компаратору
     * 
     * @param \Closure $callable - функция для сравнения (аргумент - Node)
     * 
     * @return ReferenceList - возвращает новый список
     */
    public function where (\Closure $callable): ReferenceList
    {
        $list = [];
        $node = $this->node;

        while ($node !== null)
        {
            if ($callable ($node))
                $list[] = $node->data;

            $node = $node->getNext ();
        }

        return new ReferenceList ($list);
    }

    /**
     * Объединение двух списков
     * 
     * @param ReferenceList $list - список для объединения
     * 
     * @return ReferenceList - возвращает новый список
     */
    public function union (ReferenceList $list): ReferenceList
    {
        return $this->massInsert ($list->list ());
    }

    # ArrayAccess-методы

    public function offsetExists ($offset): bool
    {
        return is_int ($offset) && $offset < $this->count && $offset >= 0;
    }

    public function offsetGet ($offset)
    {
        return $this->get ($offset);
    }

    public function offsetSet ($offset, $value): void
    {
        $this->set ($offset, $value);
    }

    public function offsetUnset ($offset): void
    {
        $this->remove ($offset);
    }
}
