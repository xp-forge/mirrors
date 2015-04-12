<?php namespace lang\mirrors;

use util\Objects;

class Annotation extends \lang\Object {
  private $mirror, $name, $value;

  /**
   * Creates a new annotation.
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  string $name
   * @param  var $value A resolveable value or NULL
   */
  public function __construct($mirror, $name, $value) {
    $this->mirror= $mirror;
    $this->name= $name;
    $this->value= $value;
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return var */
  public function value() { return $this->value ? $this->value->resolve($this->mirror->unit()) : null; }

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
}