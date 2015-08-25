<?php namespace lang\mirrors;

/**
 * Sources from which reflection can be created:
 *
 * - DEFAULT: Same as REFLECTION
 * - REFLECTION: Uses reflection if class exists, parsing code otherwise
 * - CODE: Parses code if available, using reflection otherwise
 *
 * Has special case handling to cope with situation that class is not
 * fully defined (e.g. when performing compile-time metaprogramming).
 */
abstract class Sources extends \lang\Enum {
  public static $DEFAULT, $REFLECTION, $CODE;
  private static $HHVM;

  static function __static() {
    if (defined('HHVM_VERSION')) {
      $reflect= 'FromHHVM';
    } else if (PHP_VERSION < '7.0.0') {
      $reflect= 'From';
    } else {
      $reflect= 'FromPhp7';
    }

    self::$REFLECTION= newinstance(__CLASS__, [1, 'REFLECTION'], sprintf('{
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new %1$sReflection($class, $source ?: $this);
        } else if ($class instanceof \lang\XPClass) {
          return new %1$sReflection($class->reflect(), $source ?: $this);
        }

        $literal= strtr($class, ".", "\\\\");
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return new %1$sReflection(new \ReflectionClass($literal), $source ?: $this);
        }

        $dotted= strtr($class, "\\\\", ".");
        if (\lang\ClassLoader::getDefault()->providesClass($dotted)) {
          return new %1$sCode($dotted, $source ?: $this);
        } else {
          return new FromIncomplete($literal);
        }
      }
    }', $reflect));
    self::$CODE= newinstance(__CLASS__, [2, 'CODE'], sprintf('{
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new %1$sReflection($class, $source ?: $this);
        } else if ($class instanceof \lang\XPClass) {
          return new %1$sReflection($class->reflect(), $source ?: $this);
        }

        $dotted= strtr($class, "\\\\", ".");
        if (\lang\ClassLoader::getDefault()->providesClass($dotted)) {
          return new %1$sCode($dotted, $source ?: $this);
        }

        $literal= strtr($class, ".", "\\\\");
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return new %1$sReflection(new \ReflectionClass($literal), $source ?: $this);
        } else {
          return new FromIncomplete($literal);
        }
      }
    }', $reflect));

    self::$DEFAULT= self::$REFLECTION;
  }

  /**
   * Creates a reflection source for a given class
   *
   * @param  var $class Either a lang.XPClass, a ReflectionClass or a class name
   * @param  self $source
   * @return lang.mirrors.Source
   */
  public abstract function reflect($class, $source= null);
}