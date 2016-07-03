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
    return $this->mirror->reflect->typeImplements($arg instanceof TypeMirror
      ? $arg->reflect->name
      : strtr($arg, '.', '\\')
    );
  }

  /**
   * Iterates over all interfaces
   *
   * @return php.Generator
   */
  public function getIterator() {
    $return= [];
    foreach ($this->mirror->reflect->allInterfaces() as $interface) {
      $return[]= new TypeMirror($interface);
    }
    return new \ArrayIterator($return);
  }

  /**
   * Returns only interfaces this type declares directly
   *
   * @return php.Generator
   */
  public function declared() {
    foreach ($this->mirror->reflect->declaredInterfaces() as $interface) {
      $return[]= new TypeMirror($interface);
    }
    return new \ArrayIterator($return);
  }
}