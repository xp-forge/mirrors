<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

class NoParentCodeTest extends NoParentTest {

  /** @return lang.mirrors.Source */
  protected function source() { return Sources::$CODE; }
}