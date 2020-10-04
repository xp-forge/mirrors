<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\Value;
use unittest\{Test, Values};

class ValueTest extends ResolveableTest {

  #[Test]
  public function resolve_returns_backing_null() {
    $this->assertNull((new Value(null))->resolve($this->source));
  }

  #[Test, Values([[0], [1], [-1], [1.5], [-1.5], [true], [false], [''], ['Test'], [[]], [[1, 2, 3]], [['key' => 'value']]])]
  public function resolve_returns_backing_value($value) {
    $this->assertEquals($value, (new Value($value))->resolve($this->source));
  }
}