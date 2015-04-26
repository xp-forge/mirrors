<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\TypeMirror;

abstract class ResolveableTest extends \unittest\TestCase {
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(static::class);
  }
}