<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\Value;

class ValueTest extends ResolveableTest {

  #[@test]
  public function resolve_returns_backing_value() {
    $this->assertEquals(1, (new Value(1))->resolve($this->type));
  }
}