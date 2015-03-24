<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Fields extends \lang\Object implements \IteratorAggregate {
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
   * Checks whether a given field is provided
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return $this->mirror->reflect->hasProperty($name);
  }

  /**
   * Returns a given field if provided or raises an exception
   *
   * @param  string $name
   * @return lang.reflection.Field
   * @throws lang.ElementNotFoundException
   */
  public function named($name) {
    if ($this->provides($name)) {
      return new Field($this->mirror, $this->mirror->reflect->getProperty($name));
    }
    throw new ElementNotFoundException('No field $'.$name.' in '.$this->mirror->name());
  }

  /**
   * Iterates over all fields
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->getProperties() as $method) {
      yield new Field($this->mirror, $method);
    }
  }
}