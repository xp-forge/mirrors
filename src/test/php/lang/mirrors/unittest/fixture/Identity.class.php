<?php namespace lang\mirrors\unittest\fixture;

use util\Objects;

final class Identity extends \lang\Object {
  const NAME = 'Id';
  public static $NULL;
  private $value;

  static function __static() {
    self::$NULL= new self(null);
  }

  /** @param  var $value */
  public function __construct($value) { $this->value= $value; }

  public function equals($cmp) { return $cmp instanceof self && Objects::equal($this->value, $cmp->value); }
}