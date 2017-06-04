<?php namespace lang\mirrors\unittest\fixture;

use util\Objects;

final class Identity implements \lang\Value {
  const NAME = 'Id';
  public static $NULL;
  private $value;

  static function __static() {
    self::$NULL= new self(null);
  }

  /** @param var $value */
  public function __construct($value) { $this->value= $value; }

  /** @return string */
  public function toString() { return nameof($this).'('.Objects::stringOf($this->value).')'; }

  /** @return string */
  public function hashCode() { return '@'.Objects::hashOf($this->value); }

  /**
   * Compares this identity to a given value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->value, $value->value) : 1;
  }
}