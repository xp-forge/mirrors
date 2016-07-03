<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

class NoParentReflectionTest extends NoParentTest {

  /** @return lang.mirrors.Source */
  protected function source() { return Sources::$REFLECTION; }
}