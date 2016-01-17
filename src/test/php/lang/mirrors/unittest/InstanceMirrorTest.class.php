<?php namespace lang\mirrors\unittest;

use unittest\TestCase;
use lang\mirrors\InstanceMirror;
use lang\IllegalArgumentException;

/**
 * Tests InstanceMirror
 */
class InstanceMirrorTest extends TestCase {

  #[@test]
  public function can_create() {
    new InstanceMirror($this);
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  0, 1, 1.0, -0.5,
  #  '', 'Test',
  #  [[]], [[1, 2, 3]], [['color' => 'green']],
  #  true, false, null
  #])]
  public function cannot_create_from($value) {
    new InstanceMirror($value);
  }

  #[@test]
  public function name() {
    $this->assertEquals(nameof($this), (new InstanceMirror($this))->name());
  }

  #[@test]
  public function type() {
    $this->assertEquals(typeof($this), (new InstanceMirror($this))->type());
  }
}