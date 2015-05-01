<?php namespace lang\mirrors\parse;

use util\Objects;

/**
 * Represents an instance creation expression.
 *
 * @test   xp://lang.mirrors.unittest.NewInstanceTest
 */
class NewInstance extends Resolveable {
  private $type;

  /**
   * Creates a new Newinstance instance:)
   *
   * @param  string $type
   * @param  self[] $arguments
   */
  public function __construct($type, $arguments) {
    $this->type= $type;
    $this->arguments= $arguments;
  }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($source) {
    return $source->resolve($this->type)->newInstance(array_map(
      function($arg) use($source) { return $arg->resolve($source); },
      $this->arguments
    ));
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->type === $cmp->type &&
      Objects::equal($this->arguments, $cmp->arguments)
    );
  }

  /** @return string */
  public function __toString() { return 'new '.$this->type.'('.Objects::stringOf($this->arguments).')'; }
}