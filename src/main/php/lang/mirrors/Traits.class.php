<?php namespace lang\mirrors;

class Traits implements \lang\Value, \IteratorAggregate {
  use ListOf;

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
    return $this->mirror->reflect->typeUses($arg instanceof TypeMirror
      ? $arg->reflect->name
      : strtr($arg, '.', '\\')
    );
  }

  /**
   * Iterates over all traits
   *
   * @return iterable
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->allTraits() as $trait) {
      if (0 === strncmp($trait->name, '__', 2)) continue;
      yield new TypeMirror($trait);
    }
  }

  /**
   * Returns only traits this type uses directly
   *
   * @return iterable
   */
  public function declared() {
    foreach ($this->mirror->reflect->declaredTraits() as $trait) {
      if (0 === strncmp($trait->name, '__', 2)) continue;
      yield new TypeMirror($trait);
    }
  }
}