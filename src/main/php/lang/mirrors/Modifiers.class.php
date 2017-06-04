<?php namespace lang\mirrors;

/**
 * Modifiers
 *
 * @test  xp://lang.mirrors.unittest.ModifiersTest
 * @see   https://github.com/xp-framework/xp-framework/wiki/codingstandards#modifiers
 */
class Modifiers implements \lang\Value {
  const IS_STATIC    = 0x0001;
  const IS_ABSTRACT  = 0x0002;
  const IS_FINAL     = 0x0004;
  const IS_PUBLIC    = 0x0100;
  const IS_PROTECTED = 0x0200;
  const IS_PRIVATE   = 0x0400;
  const IS_NATIVE    = 0xF000;

  private static $names= [
    'public'    => self::IS_PUBLIC,
    'protected' => self::IS_PROTECTED,
    'private'   => self::IS_PRIVATE,
    'static'    => self::IS_STATIC,
    'final'     => self::IS_FINAL,
    'abstract'  => self::IS_ABSTRACT,
    'native'    => self::IS_NATIVE
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

  /** @return bool */
  public function isStatic() { return 0 !== ($this->bits & self::IS_STATIC); }

  /** @return bool */
  public function isAbstract() { return 0 !== ($this->bits & self::IS_ABSTRACT); }

  /** @return bool */
  public function isFinal() { return 0 !== ($this->bits & self::IS_FINAL); }

  /** @return bool */
  public function isPublic() { return 0 !== ($this->bits & self::IS_PUBLIC); }

  /** @return bool */
  public function isProtected() { return 0 !== ($this->bits & self::IS_PROTECTED); }

  /** @return bool */
  public function isPrivate() { return 0 !== ($this->bits & self::IS_PRIVATE); }

  /** @return bool */
  public function isNative() { return 0 !== ($this->bits & self::IS_NATIVE); }

  /**
   * Compares a given value to this modifiers instance
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    if ($value instanceof self) {
      $diff= $this->bits - $value->bits;
      return $diff < 0 ? -1 : ($diff > 0 ? 1 : 0);
    }
    return 1;
  }

  /** @return string */
  public function hashCode() { return 'M['.$this->bits; }

  /** @return string */
  public function toString() { nameof($this).'<'.$this->names().'>'; }

}