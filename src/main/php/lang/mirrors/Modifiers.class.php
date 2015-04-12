<?php namespace lang\mirrors;

/**
 * Modifiers
 *
 * @test  xp://lang.mirrors.unittest.ModifiersTest
 */
class Modifiers extends \lang\Object {
  private static $names= [
    'public'    => MODIFIER_PUBLIC,
    'protected' => MODIFIER_PROTECTED,
    'private'   => MODIFIER_PRIVATE,
    'static'    => MODIFIER_STATIC,
    'final'     => MODIFIER_FINAL,
    'abstract'  => MODIFIER_ABSTRACT
  ];
  private $bits;

  /**
   * Creates a new modifiers instance
   *
   * @param  var $arg Either a number or a space-separated string, or an array with modifier names
   */
  public function __construct($arg) {
    if (is_string($arg)) {
      $this->bits= '' === $arg ? MODIFIER_PUBLIC : self::parse(explode(' ', $arg));
    } else if (is_array($arg)) {
      $this->bits= empty($arg) ? MODIFIER_PUBLIC : self::parse($arg);
    } else {
      $this->bits= $arg ? (int)$arg : MODIFIER_PUBLIC;
    }
  }

  /**
   * Parse names
   *
   * @param  string[] $names
   * @return int
   */
  private static function parse($names) {
    $bits= 0;
    foreach ($names as $name) {
      $bits |= self::$names[$name];
    }
    return $bits;
  }

  /** @return int */
  public function bits() { return $this->bits; }

  /**
   * Returns the modifier names as a string.
   *
   * @see   https://github.com/xp-framework/xp-framework/wiki/codingstandards#modifiers
   */
  public function names() {
    $names= '';
    foreach (self::$names as $name => $bit) {
      if ($this->bits & $bit) $names.= ' '.$name;
    }
    return substr($names, 1);
  }

  /**
   * Returns whether a given value is equal to this member
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->bits === $cmp->bits;
  }
}