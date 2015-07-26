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
      return new Method($this->mirror, $this->mirror->reflect->methodNamed($name));
    }
    throw new ElementNotFoundException('No method '.$name.'() in '.$this->mirror->name());
  }

  /**
   * Iterates over all methods
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->allMethods() as $name => $method) {
      if (0 === strncmp($name, '__', 2)) continue;
      yield new Method($this->mirror, $method);
    }
  }

  /**
   * Iterates over declared methods.
   *
   * @return php.Generator
   */
  public function declared() {
    foreach ($this->mirror->reflect->declaredMethods() as $name => $method) {
      if (0 === strncmp('__', $name, 2)) continue;
      yield new Method($this->mirror, $method);
    }
  }

  /**
   * Iterates over methods.
   *
   * @param  int $kind Either Member::$STATIC or Member::$INSTANCE bitwise-or'ed with Member::$DECLARED
   * @return php.Generator
   */
  public function of($kind) {
    $instance= ($kind & Member::$STATIC) === 0;
    $methods= ($kind & Member::$DECLARED)
      ? $this->mirror->reflect->declaredMethods()
      : $this->mirror->reflect->allMethods()
    ;
    foreach ($methods as $name => $method) {
      if (0 === strncmp('__', $name, 2) || $instance === $method['access']->isStatic()) continue;
      yield new Method($this->mirror, $method);
    }
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this as $method) {
      $s.= '  '.(string)$method."\n";
    }
    return $s.']';
  }
}