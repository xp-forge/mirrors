<?php namespace lang\mirrors;

/**
 * An XP package refers to what PHP calls a namespace.
 *
 * @test   xp://lang.mirrors.unittest.PackageTest
 */
class Package extends \lang\Object {
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
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'<'.($this->isGlobal() ? '(global)' : $this->name).'>';
  }
}