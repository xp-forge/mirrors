<?php namespace lang\mirrors;

/**
 * Modifiers
 *
 * @test  xp://lang.mirrors.unittest.ModifiersTest
 * @see   https://github.com/xp-framework/xp-framework/wiki/codingstandards#modifiers
 */
class Modifiers extends \lang\Object {
  const IS_STATIC    = 0x0001;
  const IS_ABSTRACT  = 0x0002;
  const IS_FINAL     = 0x0004;
  const IS_PUBLIC    = 0x0100;
  const IS_PROTECTED = 0x0200;
  const IS_PRIVATE   = 0x0400;

  private static $names= [
    'public'    => self::IS_PUBLIC,
    'protected' => self::IS_PROTECTED,
    'private'   => self::IS_PRIVATE,
    'static'    => self::IS_STATIC,
    'final'     => self::IS_FINAL,
    'abstract'  => self::IS_ABSTRACT
  ];
  private $bits;

  /**
   * Creates a new modifiers instance
   *
   * @param  var $arg Either a number or a space-separated string, or an array with modifier names
   */
  public function __construct($arg) {
    if (is_string($arg)) {
      $this->bits= '' === $arg ? self::IS_PUBLIC : self::parse(explode(' ', $arg));
    } else if (is_array($arg)) {
      $this->bits= empty($arg) ? self::IS_PUBLIC : self::parse($arg);
    } else {
      $this->bits= $arg ? (int)$arg : self::IS_PUBLIC;
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
   * @return string
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