<?php

/**
 * ReflectionProtect
 * 
 * Защита от Reflection API в private/protected методах и проперти для PHP 8.0.0+
 * https://github.com/deathscore13/ReflectionProtect
 */

abstract class ReflectionProtect
{
    /**
     * Защита вызова метода через Reflection API
     */
    public static function method(): void
    {
        static $exist = null;

        if ($exist === false)
            return;
        
        if ($exist === null)
        {
            if (extension_loaded('reflection') || @dl((PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '').'reflection.'.PHP_SHLIB_SUFFIX))
            {
                $exist = true;
            }
            else
            {
                $exist = false;
                return;
            }
        }

        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        if (isset($bt[2]['class']) && $bt[2]['class'] === 'ReflectionMethod')
            throw new Exception('ReflectionMethod is not allowed on '.$bt[1]['class'].'::'.$bt[1]['function'].'() method in '.
                $bt[2]['file'].' on line '.$bt[2]['line']);
    }
}

trait ReflectionProtectObjectPrivate
{
    /**
     * Установка/получение private проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление проперти с именем $name для текущего объекта. НЕОБХОДИМО вызвать в __destruct()
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт значение проперти или null
     */
    private function &__pv(string $name, mixed $value = 0, bool $destroy = false): mixed
    {
        ReflectionProtect::method();
        
        static $var = [];
        $id = spl_object_id($this);
        
        if (func_num_args() === 2)
        {
            $var[$id][$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$id][$name] ?? null;
            unset($var[$id][$name]);

            return $buffer;
        }
        else if (!isset($var[$id][$name]))
        {
            throw new Exception('Undefined property '.self::class.'::$'.$name);
        }
        
        return $var[$id][$name];
    }
}

trait ReflectionProtectObjectProtected
{
    /**
     * Установка/получение protected проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление проперти с именем $name для текущего объекта. НЕОБХОДИМО вызвать в __destruct()
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт значение проперти или null
     */
    protected function &__pt(string $name, mixed $value = 0, bool $destroy = false): mixed
    {
        ReflectionProtect::method();
        
        static $var = [];
        $id = spl_object_id($this);
        
        if (func_num_args() === 2)
        {
            $var[$id][$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$id][$name] ?? null;
            unset($var[$id][$name]);

            return $buffer;
        }
        else if (!isset($var[$id][$name]))
        {
            throw new Exception('Undefined property '.self::class.'::$'.$name);
        }
        
        return $var[$id][$name];
    }
}

trait ReflectionProtectStaticPrivate
{
    /**
     * Установка/получение статической private проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление проперти с именем $name для текущего объекта
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт значение проперти или null
     */
    private static function &__pvs(string $name, mixed $value = 0, bool $destroy = false): mixed
    {
        ReflectionProtect::method();
        
        static $var = [];
        
        if (func_num_args() === 2)
        {
            $var[$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$name] ?? null;
            unset($var[$name]);

            return $buffer;
        }
        else if (!isset($var[$name]))
        {
            throw new Exception('Undefined property '.self::class.'::$'.$name);
        }
        
        return $var[$name];
    }
}

trait ReflectionProtectStaticProtected
{
    /**
     * Установка/получение статической protected проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление проперти с именем $name для текущего объекта
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт значение проперти или null
     */
    protected static function &__pts(string $name, mixed $value = 0, bool $destroy = false): mixed
    {
        ReflectionProtect::method();
        
        static $var = [];
        
        if (func_num_args() === 2)
        {
            $var[$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$name] ?? null;
            unset($var[$name]);

            return $buffer;
        }
        else if (!isset($var[$name]))
        {
            throw new Exception('Undefined property '.self::class.'::$'.$name);
        }
        
        return $var[$name];
    }
}

trait ReflectionProtectProperty
{
    use ReflectionProtectObjectPrivate, ReflectionProtectObjectProtected, ReflectionProtectStaticPrivate, ReflectionProtectStaticProtected;
}
