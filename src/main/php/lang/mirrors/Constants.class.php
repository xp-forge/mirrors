<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Constants implements \lang\Value, \IteratorAggregate {
  use ListOf;
  private $mirror;

  /**
   * Creates a new methods instance
   *
   * @param  lang.mirrors.TypeMirror $mirror
   */
  public function __construct(TypeMirror $mirror) {
    $this->mirror= $mirror;
  }

  /**
   * Checks whether a given method is provided
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return $this->mirror->reflect->hasConstant($name);
  }

  /**
   * Returns a given method if provided or raises an exception
   *
   * @param  string $name
   * @return lang.mirrors.Constant
   * @throws lang.ElementNotFoundException
   */
  public function named($name) {
    if ($this->provides($name)) {
      return new Constant($this->mirror, $name, $this->mirror->reflect->constantNamed($name));
    }
    throw new ElementNotFoundException('No constant '.$name.' in '.$this->mirror->name());
  }

  /**
   * Iterates over all methods
   *
   * @return iterable
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->allConstants() as $name => $value) {
      yield new Constant($this->mirror, $name, $value);
    }
  }
}