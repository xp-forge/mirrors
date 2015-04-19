<?php namespace lang\mirrors;

abstract class Sources extends \lang\Enum {
  public static $DEFAULT, $REFLECTION, $CODE;

  static function __static() {
    self::$DEFAULT= newinstance(self::class, [0, 'DEFAULT'], '{
      static function __static() { }

      public function reflect($class) {
        $literal= strtr($class, ".", "\\\\");
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return self::$REFLECTION->reflect($class);
        } else {
          return self::$CODE->reflect($class);
        }
      }
    }');
    self::$REFLECTION= newinstance(self::class, [1, 'REFLECTION'], '{
      static function __static() { }

      public function reflect($class) {
        try {
          return new FromReflection(new \ReflectionClass(strtr($class, ".", "\\\\")));
        } catch (\Exception $e) {
          throw new \lang\ClassNotFoundException($class.": ".$e->getMessage());
        }
      }
    }');
    self::$CODE= newinstance(self::class, [2, 'CODE'], '{
      static function __static() { }

      public function reflect($class) { return new FromCode($class); }
    }');
  }

  /**
   * Creates a reflection source for a given class
   *
   * @param  string $class
   * @return lang.mirrors.Source
   * @throws lang.ClassNotFoundException
   */
  public abstract function reflect($class);
}