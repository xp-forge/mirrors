<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Constants extends \lang\Object implements \IteratorAggregate {
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
   * @return lang.reflection.Constant
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
   * @return php.Generator
   */
  public function getIterator() {
    $return= [];
    foreach ($this->mirror->reflect->allConstants() as $name => $value) {
      $return[]= new Constant($this->mirror, $name, $value);
    }
    return new \ArrayIterator($return);
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this as $const) {
      $s.= '  '.(string)$const."\n";
    }
    return $s.']';
  }
}