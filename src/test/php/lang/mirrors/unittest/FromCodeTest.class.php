<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

class FromCodeTest extends SourceTest {

  /**
   * Creates a new reflection source
   *
   * @param  string $name
   * @return lang.mirrors.Source
   */
  protected function reflect($name) {
    return Sources::$CODE->reflect($name);
  }
}