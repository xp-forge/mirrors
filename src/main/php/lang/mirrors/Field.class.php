<?php namespace lang\mirrors;

use lang\IllegalArgumentException;
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
   * @param  var $arg Either a ReflectionPropertsy or a string
   * @throws lang.IllegalArgumentException If there is no such field
   */
  public function __construct($mirror, $arg) {
    if ($arg instanceof \ReflectionProperty) {
      $reflect= $arg;
    } else {
      try {
        $reflect= $mirror->reflect->getProperty($arg);
      } catch (\Exception $e) {
        throw new IllegalArgumentException('No field named $'.$arg.' in '.$mirror->name());
      }
    }
    parent::__construct($mirror, $reflect);
    $reflect->setAccessible(true);
  }

  /**
   * Returns the field's type
   *
   * @return lang.Type
   */
  public function type() {
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
    if ($this->reflect->isStatic()) {
      return $this->reflect->getValue(null);
    } else if ($instance && $this->reflect->getDeclaringClass()->isInstance($instance)) {
      return $this->reflect->getValue($instance);
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $this->name()
    ));
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
    if ($this->reflect->isStatic()) {
      $this->reflect->setValue(null, $value);
      return;
    } else if ($instance && $this->reflect->getDeclaringClass()->isInstance($instance)) {
      $this->reflect->setValue($instance, $value);
      return;
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $this->name()
    ));
  }

  /** @return string */
  public function __toString() {
    return $this->modifiers()->names().' '.$this->type().' $'.$this->name();
  }
}