<?php namespace lang\mirrors;

class Traits extends \lang\Object implements \IteratorAggregate {
  private $mirror;

  /**
   * Creates a new traits instance
   *
   * @param  lang.mirrors.TypeMirror $mirror
   */
  public function __construct(TypeMirror $mirror) {
    $this->mirror= $mirror;
  }

  /**
   * Checks whether a given trait is contained in this collection
   *
   * @param  var $arg
   * @return bool
   */
  public function contains($arg) {
    if ($arg instanceof TypeMirror) {
      $name= $arg->reflect->getName();
    } else {
      $name= strtr($arg, '.', '\\');
    }
    return in_array($name, $this->mirror->reflect->getTraitNames());
  }

  /**
   * Iterates over all fields
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->getTraits() as $trait) {
      yield new TypeMirror($trait);
    }
  }
}