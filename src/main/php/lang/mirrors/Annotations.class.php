<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

class Annotations implements \lang\Value, \IteratorAggregate {
  use ListOf;

  private $mirror, $backing;

  /**
   * Creates a new annotations instance
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  [:var] $backing As parsed via ClassSyntax
   */
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
   * @return lang.mirrors.Annotation
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
   * @return iterable
   */
  public function getIterator() {
    foreach ($this->backing as $name => $value) {
      yield new Annotation($this->mirror, $name, $value);
    }
  }
}