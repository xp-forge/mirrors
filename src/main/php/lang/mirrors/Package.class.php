<?php namespace lang\mirrors;

/**
 * An XP package refers to what PHP calls a namespace.
 *
 * @test   xp://lang.mirrors.unittest.PackageTest
 */
class Package extends \lang\Object {
  private $name;

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
    return $this->getClassName().'<'.$this->name().'>';
  }
}