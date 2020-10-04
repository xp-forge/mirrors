<?php namespace lang\mirrors\unittest;

use lang\mirrors\{Sources, TypeMirror};

class Php7CodeTypesTest extends Php7TypesTest {

  /**
   * Returns a fixture for a given class declaration
   *
   * @param  lang.XPClass $class
   * @return lang.mirrors.TypeMirror
   */
  protected function newFixture($class) {
    return new TypeMirror($class, Sources::$CODE);
  }
}