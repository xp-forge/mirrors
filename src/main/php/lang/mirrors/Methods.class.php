<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Methods extends \lang\Object implements \IteratorAggregate {
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
    return 0 === strncmp($name, '__', 2) ? false : $this->mirror->reflect->hasMethod($name);
  }

  /**
   * Returns a given method if provided or raises an exception
   *
   * @param  string $name
   * @return lang.reflection.Method
   * @throws lang.ElementNotFoundException
   */
  public function named($name) {
    if ($this->provides($name)) {
      return new Method($this->mirror, $this->mirror->reflect->getMethod($name));
    }
    throw new ElementNotFoundException('No method '.$name.'() in '.$this->mirror->name());
  }

  /**
   * Iterates over all methods
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->getMethods() as $method) {
      if (0 !== strncmp($method->getName(), '__', 2)) {
        yield new Method($this->mirror, $method);
      }
    }
  }

  /**
   * Iterates over methods.
   *
   * @param  int $kind Either Member::$STATIC or Member::$INSTANCE
   * @return php.Generator
   */
  public function of($kind) {
    foreach ($this->mirror->reflect->getMethods() as $method) {
      if (0 === strncmp('__', $method->name, 2) || $kind === ($method->getModifiers() & MODIFIER_STATIC)) continue;
      yield new Method($this->mirror, $method);
    }
  }
}