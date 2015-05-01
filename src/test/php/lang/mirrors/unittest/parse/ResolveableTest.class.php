<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\Sources;

abstract class ResolveableTest extends \unittest\TestCase {
  protected $source;

  /** @return void */
  public function setUp() {
    $this->source= Sources::$DEFAULT->reflect(static::class);
  }
}