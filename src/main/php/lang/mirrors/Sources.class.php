<?php namespace lang\mirrors;

/**
 * Sources from which reflection can be created:
 *
 * - DEFAULT: Uses reflection if class exists, parsing code otherwise
 * - REFLECTION: Uses ext/reflection
 * - CODE: Parses code.
 *
 * Has special case handling to cope with situation that class is not
 * fully defined (e.g. when performing compile-time metaprogramming).
 */
abstract class Sources extends \lang\Enum {
  public static $DEFAULT, $REFLECTION, $CODE;
  private static $HHVM;

  static function __static() {
    $reflect= defined('HHVM_VERSION') ? 'FromHHVM' : 'From';
    self::$DEFAULT= newinstance(self::class, [0, 'DEFAULT'], sprintf('{
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new %1$sReflection($class, $source ?: $this);
        }

        $literal= strtr($class, ".", "\\\\");
        $dotted= strtr($class, "\\\\", ".");
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return self::$REFLECTION->reflect($class, $source ?: $this);
        } else if (\lang\ClassLoader::getDefault()->providesClass($dotted)) {
          return new %1$sCode($dotted, $source ?: $this);
        } else {
          return new FromIncomplete($literal);
        }
      }
    }', $reflect));
    self::$REFLECTION= newinstance(self::class, [1, 'REFLECTION'], sprintf('{
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new %1$sReflection($class, $source);
        }

        try {
          return new %1$sReflection(new \ReflectionClass(strtr($class, ".", "\\\\")), $source);
        } catch (\Exception $e) {
          throw new \lang\ClassNotFoundException($class.": ".$e->getMessage());
        }
      }
    }', $reflect));
    self::$CODE= newinstance(self::class, [2, 'CODE'], sprintf('{
      static function __static() { }

      public function reflect($class, $source= null) {
        return new %1$sCode(strtr($class, "\\\\", "."), $source);
      }
    }', $reflect));
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