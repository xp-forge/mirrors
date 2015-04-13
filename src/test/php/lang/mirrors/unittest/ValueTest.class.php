<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\Value;

class ValueTest extends ResolveableTest {

  #[@test]
  public function resolve_returns_backing_null() {
    $this->assertNull((new Value(null))->resolve($this->type));
  }

  #[@test, @values([
  #  [0], [1], [-1], [1.5], [-1.5],
  #  [true], [false],
  #  [''], ['Test'],
  #  [[]], [[1, 2, 3]], [['key' => 'value']]
  #])]
  public function resolve_returns_backing_value($value) {
    $this->assertEquals($value, (new Value($value))->resolve($this->type));
  }
}