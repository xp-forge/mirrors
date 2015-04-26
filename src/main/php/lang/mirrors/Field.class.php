<?php namespace lang\mirrors;

use lang\Generic;
use lang\Type;

/**
 * A class field
 *
 * @test   xp://lang.mirrors.unittest.FieldTest
 */
class Field extends Member {
  protected static $kind= 'field';
  protected static $tags= [];

  /**
   * Creates a new field
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @param  var $arg A map returned from Source::fieldNamed(), a ReflectionProperty or a string
   * @throws lang.IllegalArgumentException If there is no such field
   */
  public function __construct($mirror, $arg) {
    if (is_array($arg)) {
      parent::__construct($mirror, $arg);
    } else if ($arg instanceof \ReflectionProperty) {
      parent::__construct($mirror, $mirror->reflect->fieldNamed($arg->name));
    } else {
      parent::__construct($mirror, $mirror->reflect->fieldNamed($arg));
    }
  }

  /**
   * Returns the field's type
   *
   * @return lang.Type
   */
  public function type() {
    if (isset($this->reflect['type'])) {
      return Type::forName($this->reflect['type']);
    }

    $tags= $this->tags();
    if (isset($tags['type'])) {
      return Type::forName($tags['type'][0]);
    } else if (isset($tags['var'])) {
      return Type::forName($tags['var'][0]);
    } else {
      return Type::$VAR;
    }
  }

  /**
   * Read this field's value
   *
   * @param  lang.Generic $instance
   * @return var
   * @throws lang.IllegalArgumentException
   */
  public function read(Generic $instance= null) {
    return $this->reflect['read']($instance);
  }

  /**
   * Modify this field's value
   *
   * @param  lang.Generic $instance
   * @param  var $value
   * @return void
   * @throws lang.IllegalArgumentException
   */
  public function modify(Generic $instance= null, $value) {
    return $this->reflect['modify']($instance, $value);
  }

  /** @return string */
  public function __toString() {
    return $this->modifiers()->names().' '.$this->type().' $'.$this->name();
  }
}