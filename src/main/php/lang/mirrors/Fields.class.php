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
    if (0 === strncmp('__', $name, 2)) return false;
    return $this->mirror->reflect->hasField($name);
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
      return new Field($this->mirror, $this->mirror->reflect->fieldNamed($name));
    }
    throw new ElementNotFoundException('No field $'.$name.' in '.$this->mirror->name());
  }

  /**
   * Iterates over all fields
   *
   * @return php.Generator
   */
  public function getIterator() {
    foreach ($this->mirror->reflect->allFields() as $name => $field) {
      if (0 === strncmp('__', $name, 2)) continue;
      yield new Field($this->mirror, $field);
    }
  }

  /**
   * Iterates over declared fields.
   *
   * @return php.Generator
   */
  public function declared() {
    foreach ($this->mirror->reflect->declaredFields() as $name => $field) {
      if (0 === strncmp('__', $name, 2)) continue;
      yield new Field($this->mirror, $field);
    }
  }

  /**
   * Iterates over fields.
   *
   * @param  int $kind Either Member::$STATIC or Member::$INSTANCE bitwise-or'ed with Member::$DECLARED
   * @return php.Generator
   */
  public function of($kind) {
    $instance= ($kind & Member::$STATIC) === 0;
    $fields= ($kind & Member::$DECLARED)
      ? $this->mirror->reflect->declaredFields()
      : $this->mirror->reflect->allFields()
    ;
    foreach ($fields as $name => $field) {
      if (0 === strncmp('__', $name, 2) || $instance === $field['access']->isStatic()) continue;
      yield new Field($this->mirror, $field);
    }
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this as $field) {
      $s.= '  '.(string)$field."\n";
    }
    return $s.']';
  }
}