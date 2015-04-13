<?php namespace lang\mirrors;

abstract class Sources extends \lang\Enum {
  public static $CODE, $REFLECTION;

  static function __static() {
    self::$REFLECTION= newinstance(self::class, [0, 'REFLECTION'], '{
      static function __static() { }

      public function reflect($name) {
        try {
          return new FromReflection(new \ReflectionClass(strtr($name, ".", "\\\\")));
        } catch (\Exception $e) {
          throw new \lang\ClassNotFoundException($name.": ".$e->getMessage());
        }
      }
    }');
    self::$CODE= newinstance(self::class, [1, 'CODE'], '{
      static function __static() { }

      public function reflect($name) { return new FromCode($name); }
    }');
  }

  public abstract function reflect($name);
}