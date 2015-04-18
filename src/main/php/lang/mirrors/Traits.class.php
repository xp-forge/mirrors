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
    return $this->mirror->reflect->typeUses(strtr(
      $arg instanceof TypeMirror ? $arg->name() : $arg,
      '.',
      '\\'
    ));
  }

  /**
   * Iterates over all traits
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->allTraits() as $trait) {
      yield new TypeMirror($trait);
    }
  }

  /**
   * Returns only traits this type uses directly
   *
   * @return php.Generator
   */
  public function declared() {
    foreach ($this->mirror->reflect->declaredTraits() as $trait) {
      yield new TypeMirror($trait);
    }
  }
}