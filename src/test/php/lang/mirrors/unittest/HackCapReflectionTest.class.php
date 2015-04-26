<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

#[@action(new OnlyOnHHVM())]
class HackCapReflectionTest extends HackCapTest {

  /** @return lang.mirrors.Sources */
  protected function source() { return Sources::$REFLECTION; }
}