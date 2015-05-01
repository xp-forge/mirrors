<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

#[@action(new OnlyOnHHVM())]
class HackAnnotationsReflectionTest extends HackAnnotationsTest {

  /** @return lang.mirrors.Sources */
  protected function source() { return Sources::$REFLECTION; }
}