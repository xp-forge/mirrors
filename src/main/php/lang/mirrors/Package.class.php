<?php namespace lang\mirrors;

/**
 * An XP package refers to what PHP calls a namespace.
 *
 * @test   xp://lang.mirrors.unittest.PackageTest
 */
class Package implements \lang\Value {
  private $name;
  public static $GLOBAL;

  static function __static() {
    self::$GLOBAL= new self('');
  }

  /**
   * Creates a package instance
   *
   * @param  string $name Either dotted or backslashed
   */
  public function __construct($name) {
    $this->name= strtr($name, '\\', '.');
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return string */
  public function declaration() { return substr($this->name, strrpos($this->name, '.') + 1); }

  /** @return bool */
  public function isGlobal() { return '' === $this->name; }

  /**
   * Compares a given value to this source
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->name, $value->name) : 1;
  }

  /** @return string */
  public function hashCode() { return 'R'.md5($this->name); }

  /** @return string */
  public function toString() { return nameof($this).'<'.($this->isGlobal() ? '(global)' : $this->name).'>'; }

}