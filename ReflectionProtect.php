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
     * 
     * @param bool $throw       false чтобы завершить скрипт, true чтобы вернуть исключение
     */
    public static function method(bool $throw = false): void
    {
        static $exist = null;

        if ($exist === false)
            return;
        
        if ($exist === null)
        {
            if (extension_loaded('reflection') || @dl((PHP_SHLIB_SUFFIX === 'so' ? '' : 'php_').'reflection.'.PHP_SHLIB_SUFFIX))
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
        {
            $err = 'ReflectionMethod is not allowed on '.$bt[1]['class'].'::'.$bt[1]['function'].'() method in '.
                $bt[2]['file'].' on line '.$bt[2]['line'];
            if ($throw)
                throw new Exception($err);
            else
                exit($err);
        }
    }
}

trait ReflectionProtectObjectPrivate
{
    /**
     * Установка поведения при обнаружении вызова __pv() через Reflection API
     * По умолчанию - false
     * 
     * @param bool $value       false чтобы завершить скрипт, true чтобы вернуть исключение, пропуск - ничего не установится
     * 
     * @return bool             Текущее значение
     */
    private function __pvThrow(bool $value = false): bool
    {
        static $throw = false;

        if (func_num_args())
            $throw = $value;
        
        return $throw;
    }

    /**
     * Установка/получение private проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление всех проперти для текущего объекта. НЕОБХОДИМО вызвать в __destruct()
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт массив уничтоженных элементов
     */
	private function &__pv(string $name, mixed $value = 0, bool $destroy = false): mixed
	{
		ReflectionProtect::method($this->__pvThrow());
		
		static $var = [];
        $id = spl_object_id($this);
		
		if (func_num_args() === 2)
        {
			$var[$id][$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$id];
            unset($var[$id]);

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
     * Установка поведения при обнаружении вызова __pt() через Reflection API
     * По умолчанию - false
     * 
     * @param bool $value       false чтобы завершить скрипт, true чтобы вернуть исключение, пропуск - ничего не установится
     * 
     * @return bool             Текущее значение
     */
    protected function __ptThrow(bool $value = false): bool
    {
        static $throw = false;

        if (func_num_args())
            $throw = $value;
        
        return $throw;
    }

    /**
     * Установка/получение protected проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * @param bool $destroy     Удаление всех проперти для текущего объекта. НЕОБХОДИМО вызвать в __destruct()
     * 
     * @return mixed            Значение проперти. При $destroy = true вернёт массив уничтоженных элементов
     */
	protected function &__pt(string $name, mixed $value = 0, bool $destroy = false): mixed
	{
		ReflectionProtect::method($this->__ptThrow());
		
		static $var = [];
        $id = spl_object_id($this);
		
		if (func_num_args() === 2)
        {
			$var[$id][$name] = $value;
        }
        else if ($destroy)
        {
            $buffer = $var[$id];
            unset($var[$id]);

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
     * Установка поведения при обнаружении вызова __pvs() через Reflection API
     * По умолчанию - false
     * 
     * @param bool $value       false чтобы завершить скрипт, true чтобы вернуть исключение, пропуск - ничего не установится
     * 
     * @return bool             Текущее значение
     */
    private static function __pvsThrow(bool $value = false): bool
    {
        static $throw = false;

        if (func_num_args())
            $throw = $value;
        
        return $throw;
    }

    /**
     * Установка/получение статической private проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * 
     * @return mixed            Значение проперти
     */
	private static function &__pvs(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method(self::__pvsThrow());
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined property '.self::class.'::$'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectStaticProtected
{
    /**
     * Установка поведения при обнаружении вызова __pts() через Reflection API
     * По умолчанию - false
     * 
     * @param bool $value       false чтобы завершить скрипт, true чтобы вернуть исключение, пропуск - ничего не установится
     * 
     * @return bool             Текущее значение
     */
    protected static function __ptsThrow(bool $value = false): bool
    {
        static $throw = false;

        if (func_num_args())
            $throw = $value;
        
        return $throw;
    }

    /**
     * Установка/получение статической protected проперти
     * 
     * @param string $name      Имя проперти
     * @param mixed $value      Если параметр указан, то установит новое значение
     * 
     * @return mixed            Значение проперти
     */
	protected static function &__pts(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method(self::__ptsThrow());
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined property '.self::class.'::$'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectProperty
{
    use ReflectionProtectObjectPrivate, ReflectionProtectObjectProtected, ReflectionProtectStaticPrivate, ReflectionProtectStaticProtected;
}
