<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\ElementNotFoundException;

/**
 * Tests TypeMirror
 */
class TypeMirrorTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new TypeMirror(self::class);
  }

  #[@test]
  public function name() {
    $this->assertEquals('lang.mirrors.unittest.TypeMirrorTest', (new TypeMirror(self::class))->name());
  }

  #[@test]
  public function comment() {
    $this->assertEquals('Tests TypeMirror', (new TypeMirror(self::class))->comment());
  }

  #[@test]
  public function this_class_has_parent() {
    $this->assertEquals('unittest.TestCase', (new TypeMirror(self::class))->parent()->name());
  }

  #[@test]
  public function object_class_does_not_have_a_parent() {
    $this->assertNull((new TypeMirror('lang.Object'))->parent());
  }

  #[@test]
  public function this_class_has_constructor() {
    $this->assertInstanceOf('lang.mirrors.Constructor', (new TypeMirror(self::class))->constructor());
  }

  #[@test]
  public function object_class_does_not_have_constructor() {
    $this->assertNull((new TypeMirror('lang.Object'))->constructor());
  }
}