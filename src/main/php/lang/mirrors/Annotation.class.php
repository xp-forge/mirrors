<?php namespace lang\mirrors;

use util\Objects;

class Annotation extends \lang\Object {
  private $mirror, $name, $value;

  /**
   * Creates a new annotation.
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  string $name
   * @param  lang.mirrors.parse.Resolveable $value A resolveable value or NULL
   */
  public function __construct($mirror, $name, $value= null) {
    $this->mirror= $mirror;
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value ? $this->value->resolve($this->mirror->unit()) : null; }

  /**
   * Returns the type this member was declared in.
   *
   * @return lang.mirrors.TypeMirror
   */
  public function declaredIn() { return $this->mirror; }

  /**
   * Returns whether a given value is equal to this annotation
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->name === $cmp->name &&
      Objects::equal($this->value, $cmp->value)
    );
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this.')';
  }

  /** @return string */
  public function __toString() {
    return '@'.$this->name.(null === $this->value ? '' : '('.$this->value.')');
  }
}