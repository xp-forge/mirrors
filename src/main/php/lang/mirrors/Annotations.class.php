<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Annotations extends \lang\Object implements \IteratorAggregate {
  private $mirror, $backing;

  public function __construct($mirror, array $backing) {
    $this->mirror= $mirror;
    $this->backing= $backing;
  }

  /**
   * Returns whether annotations are present
   *
   * @return bool
   */
  public function present() { return !empty($this->backing); }

  /**
   * Checks whether a given method is provided
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return array_key_exists($name, $this->backing);
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
      return new Annotation($this->mirror, $name, $this->backing[$name]);
    }
    throw new ElementNotFoundException('No annotation @'.$name.' in '.$this->mirror->name());
  }

  /**
   * Iterates over all methods
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->backing as $name => $value) {
      yield new Annotation($this->mirror, $name, $value);
    }
  }
}