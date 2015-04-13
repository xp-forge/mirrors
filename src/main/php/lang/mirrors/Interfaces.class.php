<?php namespace lang\mirrors;

class Interfaces extends \lang\Object implements \IteratorAggregate {
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
   * @param  var $arg Either a string or a type mirror
   * @return bool
   */
  public function contains($arg) {
    return $this->mirror->reflect->implementsInterface($arg instanceof TypeMirror
      ? strtr($arg->name(), '.', '\\')
      : strtr($arg, '.', '\\')
    );
  }

  /**
   * Iterates over all interfaces
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->getInterfaces() as $interface) {
      yield new TypeMirror($interface);
    }
  }

  /**
   * Returns only interfaces this type declares directly
   *
   * @return php.Generator
   */
  public function declared() {
    $parent= $this->mirror->reflect->getParentClass();
    $inherited= $parent ? array_flip($parent->getInterfaceNames()) : [];
    foreach ($this->mirror->reflect->getInterfaces() as $interface) {
      if (isset($inherited[$interface->getName()])) continue;
      yield new TypeMirror($interface);
    }
  }
}