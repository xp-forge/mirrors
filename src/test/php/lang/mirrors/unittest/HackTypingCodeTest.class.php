<?php namespace lang\mirrors\unittest;

use lang\mirrors\Sources;

#[@action(new OnlyOnHHVM())]
class HackTypingCodeTest extends HackTypingTest {

  /** @return lang.mirrors.Sources */
  protected function source() { return Sources::$CODE; }

}