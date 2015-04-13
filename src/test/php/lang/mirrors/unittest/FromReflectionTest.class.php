<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

class FromReflectionTest extends SourceTest {

  /**
   * Creates a new reflection source
   *
   * @param  string $name
   * @return lang.mirrors.Source
   */
  protected function reflect($name) {
    return Sources::$REFLECTION->reflect($name);
  }
}