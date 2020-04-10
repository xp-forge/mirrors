<?php namespace lang\mirrors\parse;

/**
 * Fetches code unit for a given class
 */
class ClassSyntax {
  const CACHE_LIMIT = 20;
  private static $cache= [];
  private static $syntax;

  static function __static() {
    self::$syntax= new PhpSyntax();
  }

  /**
   * Parses a class
   *
   * @param  string $class Fully qualified class name
   * @return lang.mirrors.parse.CodeUnit
   */
  public function codeUnitOf($class) {
    if (!isset(self::$cache[$class])) {
      $source= new ClassSource($class);
      if ($source->present()) {
        self::$cache[$class]= self::$syntax->parse($source);
        while (sizeof(self::$cache) > self::CACHE_LIMIT) {
          unset(self::$cache[key(self::$cache)]);
        }
      } else {
        return CodeUnit::ofIcomplete($class);
      }
    }
    return self::$cache[$class];
  }
}