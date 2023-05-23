# ReflectionProtect
### Защита от Reflection API в private/protected методах и проперти для PHP 8.0.0+<br><br>

`ReflectionProtect::method()` существует сам по себе, отдельно от трейтов для подключения проперти<br><br>
Так как у `__pv()`, `__pt()`, `__pvs()` и `__pts()` разный буфер, то можно использовать одинаковые имена проперти<br><br>
**ОБРАТИТЕ ВНИМАНИЕ, что для объектных private/protected проперти нужно вызывать `__pv('', 0, true)`/`__pt('', 0, true)` в `__destruct()`**<br><br>
Советую открыть **`ReflectionProtect.php`** и почитать описания `ReflectionProtect::method()`, `__pvThrow()`, `__pv()`, `__ptThrow()`, `__pt()`, `__pvsThrow()`, `__pvs()`, `__ptsThrow()` и `__pts()`

<br><br>
## Подключение проперти
`use ReflectionProtectObjectPrivate;` - добавление private проперти<br>
`use ReflectionProtectObjectProtected;` - добавление protected проперти<br>
`use ReflectionProtectStaticPrivate;` - добавление статических private проперти<br>
`use ReflectionProtectStaticProtected;` - добавление статических protected проперти<br>
`use ReflectionProtectProperty;` - добавление всех типов проперти

<br><br>
## Пример использования
**`main.php`**:
```php
// определение поведения защиты по умолчанию (false - завершение скрипта, true - исключение)
const REFLECTION_PROTECT_THROW = false;

// подключение ReflectionProtect
require('ReflectionProtect.php');

class BaseClass
{
    // подключение объектных и статических private проперти
    use ReflectionProtectObjectPrivate, ReflectionProtectStaticPrivate;

    public function __construct()
    {
        // при обнаружении вызова __pv() через Reflection API скрипт завершится
        $this->__pvThrow(false);

        // инициализация объектного проперти
        $this->__pv('var1', 0);
    }

    public function __destruct()
    {
        // удаление всех private проперти для текущего объекта
        $this->__pv('', 0, true);
    }

    // для инициализации статических проперти придётся сделать отдельную функцию
    public static function __init()
    {
        // при обнаружении вызова __pvs() через Reflection API функция вернёт исключение
        $this->__pvThrow(true);

        // инициализация статического проперти
        self::__pvs('var2', 2);
    }

    public static function method(): void
    {
        // для public методов это тоже работает, но на них нет смысла использовать ReflectionMethod
        ReflectionProtect::method(true);
        echo('BaseClass::method()'.PHP_EOL);
    }

    private function privateMethod(): void
    {
        // true означает что нужно вернуть исключение, а не завершить скрипт
        ReflectionProtect::method(true);
        echo('BaseClass::privateMethod()'.PHP_EOL);
    }

    public function privateProperty(): void
    {
        // вывод: 0
        echo($this->__pv('var1').PHP_EOL);

        // получаем private проперти var1 по ссылке. используйте это если у вас несколько операций с этим проперти
        $var1 = &$this->__pv('var1');

        // меняем значение на 1
        $var1 = 1;

        // вывод: 1
        echo($this->__pv('var1').PHP_EOL);
    }

    public static function privateStaticProperty(): void
    {
        // вывод: 2
        echo(self::__pvs('var2').PHP_EOL);

        // значение проперти можно установить через второй параметр. используйте это если у вас только одна операция с этим проперти
        self::__pvs('var2', 3);

        // вывод: 3
        echo(self::__pvs('var2').PHP_EOL);
    }
}

// создание объекта класса (инициализация var1)
$c = new BaseClass();

// ручная инициализация статической проперти var2
BaseClass::__init();

// обычный вызов метода
BaseClass::method();

// вызов приватного метода через ReflectionMethod
$r = new ReflectionMethod('BaseClass', 'privateMethod');
$r->setAccessible(true);
try {
    $r->invoke(null);
} catch (Exception $e) {
    // ошибка при вызове :(
    echo('BaseClass::privateMethod() failed'.PHP_EOL);
}

// тест private проперти
$c->privateProperty();

// тест статических private проперти
BaseClass::privateStaticProperty();

// попытка "взломать" ReflectionProtect
$r = new ReflectionMethod($c, '__pv');
$r->setAccessible(true);
try {
    // установка значения 123 для проперти var1 и вывод
    echo($r->invoke($c, 'var1', 123).PHP_EOL);
} catch (Exception $e) {
    // этот текст не выведется, т.к. вызов методов ReflectionProtectProperty через ReflectionMethod приведёт к завершению скрипта
    echo('BaseClass::__pv() failed'.PHP_EOL);
}
```
