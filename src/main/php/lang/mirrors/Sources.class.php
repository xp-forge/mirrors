<?php namespace lang\mirrors;

use lang\{ClassLoader, Enum, XPClass};

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
abstract class Sources extends Enum {
  public static $DEFAULT, $REFLECTION, $CODE;
  private static $HHVM;

  static function __static() {
    self::$REFLECTION= new class(1, 'REFLECTION') extends Sources {
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new FromReflection($class, $source ?: $this);
        } else if ($class instanceof XPClass) {
          return new FromReflection($class->reflect(), $source ?: $this);
        }

        $literal= strtr($class, '.', '\\');
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return new FromReflection(new \ReflectionClass($literal), $source ?: $this);
        }

        $dotted= strtr($class, '\\', '.');
        if (ClassLoader::getDefault()->providesClass($dotted)) {
          return new FromCode($dotted, $source ?: $this);
        } else {
          return new FromIncomplete($literal);
        }
      }
    };
    self::$CODE= new class(2, 'CODE') extends Sources {
      static function __static() { }

      public function reflect($class, $source= null) {
        if ($class instanceof \ReflectionClass) {
          return new FromReflection($class, $source ?: $this);
        } else if ($class instanceof XPClass) {
          return new FromReflection($class->reflect(), $source ?: $this);
        }

        $dotted= strtr($class, '\\', '.');
        if (ClassLoader::getDefault()->providesClass($dotted)) {
          return new FromCode($dotted, $source ?: $this);
        }

        $literal= strtr($class, '.', '\\');
        if (class_exists($literal) || interface_exists($literal) || trait_exists($literal)) {
          return new FromReflection(new \ReflectionClass($literal), $source ?: $this);
        } else {
          return new FromIncomplete($literal);
        }
      }
    };

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