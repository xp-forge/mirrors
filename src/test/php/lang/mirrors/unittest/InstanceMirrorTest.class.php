<?php namespace lang\mirrors\unittest;

use lang\IllegalArgumentException;
use lang\mirrors\InstanceMirror;
use unittest\{Expect, Test, TestCase, Values};

/**
 * Tests InstanceMirror
 */
class InstanceMirrorTest extends TestCase {

  #[Test]
  public function can_create() {
    new InstanceMirror($this);
  }

  #[Test, Expect(IllegalArgumentException::class), Values([0, 1, 1.0, -0.5, '', 'Test', [[]], [[1, 2, 3]], [['color' => 'green']], true, false, null])]
  public function cannot_create_from($value) {
    new InstanceMirror($value);
  }

  #[Test]
  public function name() {
    $this->assertEquals(nameof($this), (new InstanceMirror($this))->name());
  }

  #[Test]
  public function type() {
    $this->assertEquals(typeof($this), (new InstanceMirror($this))->type());
  }
}