<?php namespace lang\mirrors;

use lang\IllegalArgumentException;

class InstanceMirror extends TypeMirror {

  /**
   * Creates a new instance mirror
   *
   * @param  var $value
   */
  public function __construct($value) {
    if (is_object($value)) {

      // Parent constructor inlined
      $this->reflect= Sources::$REFLECTION->reflect(new \ReflectionObject($value));
    } else {
      throw new IllegalArgumentException('Given value is not an object, '.typeof($value).' given');
    }
  }
}   