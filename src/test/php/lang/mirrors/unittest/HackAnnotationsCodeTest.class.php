<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

class HackAnnotationsCodeTest extends HackAnnotationsTest {

  /** @return lang.mirrors.Sources */
  protected function source() { return Sources::$CODE; }
}