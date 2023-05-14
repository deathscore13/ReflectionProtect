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
     * Защита вызова метода через ReflectionMethod
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
            if (extension_loaded('Reflection') || @dl((PHP_SHLIB_SUFFIX === 'so' ? '' : 'php_').'reflection.'.PHP_SHLIB_SUFFIX))
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
     * Установка/получение private переменной
     * 
     * @param string $name      Имя переменной
     * @param mixed $value      Если параметр указан, то установит переменной новое значение
     * 
     * @return mixed            Значение переменной
     */
	private function &__pv(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method();
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined variable $'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectObjectProtected
{
    /**
     * Установка/получение protected переменной
     * 
     * @param string $name      Имя переменной
     * @param mixed $value      Если параметр указан, то установит переменной новое значение
     * 
     * @return mixed            Значение переменной
     */
	protected function &__pt(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method();
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined variable $'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectStaticPrivate
{
    /**
     * Установка/получение статической private переменной
     * 
     * @param string $name      Имя переменной
     * @param mixed $value      Если параметр указан, то установит переменной новое значение
     * 
     * @return mixed            Значение переменной
     */
	private static function &__pvs(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method();
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined variable $'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectStaticProtected
{
    /**
     * Установка/получение статической protected переменной
     * 
     * @param string $name      Имя переменной
     * @param mixed $value      Если параметр указан, то установит переменной новое значение
     * 
     * @return mixed            Значение переменной
     */
	protected static function &__pts(string $name, mixed $value = 0): mixed
	{
		ReflectionProtect::method();
		
		static $var = [];
		
		if (func_num_args() === 2)
			$var[$name] = $value;
		else if (!isset($var[$name]))
			throw new Exception('Undefined variable $'.$name);
		
		return $var[$name];
	}
}

trait ReflectionProtectProperty
{
    use ReflectionProtectObjectPrivate, ReflectionProtectObjectProtected, ReflectionProtectStaticPrivate, ReflectionProtectStaticProtected;
}
