<?php namespace lang\mirrors;

abstract class Sources extends \lang\Enum {
  public static $DEFAULT, $REFLECTION, $CODE;

  static function __static() {
    self::$DEFAULT= newinstance(self::class, [0, 'DEFAULT'], '{
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new FromReflection($class, $source ?: $this);
        } else {
          $literal= strtr($class, ".", "\\\\");
          $dotted= strtr($class, "\\\\", ".");
          if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
            return self::$REFLECTION->reflect($class, $source ?: $this);
          } else if (\lang\ClassLoader::getDefault()->providesClass($dotted)) {
            return new FromCode($dotted, $source ?: $this);
          } else {
            return new FromIncomplete($literal);
          }
        }
      }
    }');
    self::$REFLECTION= newinstance(self::class, [1, 'REFLECTION'], '{
      static function __static() { }

      public function reflect($class, $source= null) {
        try {
          return new FromReflection(
            $class instanceof \ReflectionClass ? $class : new \ReflectionClass(strtr($class, ".", "\\\\")),
            $source
          );
        } catch (\Exception $e) {
          throw new \lang\ClassNotFoundException($class.": ".$e->getMessage());
        }
      }
    }');
    self::$CODE= newinstance(self::class, [2, 'CODE'], '{
      static function __static() { }

      public function reflect($class, $source= null) {
        return new FromCode(strtr($class, "\\\\", "."), $source);
      }
    }');
  }

  /**
   * Creates a reflection source for a given class
   *
   * @param  string $class
   * @param  self $source
   * @return lang.mirrors.Source
   * @throws lang.ClassNotFoundException
   */
  public abstract function reflect($class, $source= null);
}