<?php namespace lang\mirrors\parse;

use lang\XPClass;

/**
 * Reference type
 *
 * @test  xp://lang.mirrors.unittest.ReferenceTypeRefTest
 */
class ReferenceTypeRef extends Resolveable {
  private $name;

  /**
   * Creates a new reference type reference
   *
   * @param  string $name Qualified or unqualified as well as "self" and "parent" keywords
   */
  public function __construct($name) {
    $this->name= $name;
  }

  /** @return string */
  public function name() { return $this->name; }

  /**
   * Resolve this value 
   *
   * @param  lang.mirrors.Source $source
   * @return var
   */
  public function resolve($source) {
    if (!$source instanceof \lang\mirrors\Source) {
      (new \lang\IllegalArgumentException('Requires source'))->printStackTrace();
      exit;
    }
    return XPClass::forName($source->resolve($this->name)->typeName());
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }

  /** @return string */
  public function __toString() { return $this->name; }
}