<?php namespace lang\mirrors;

use lang\IllegalArgumentException;
use lang\Generic;

class Field extends Member {
  protected static $kind= 'field';

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

  /** @return string */
  protected function kind() { return 'field'; }

  /**
   * Gets this field's value
   *
   * @param  lang.Generic $instance
   * @return var
   * @throws lang.IllegalArgumentException
   */
  public function get(Generic $instance= null) {
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
   * Sets this field's value
   *
   * @param  lang.Generic $instance
   * @param  var $value
   * @return void
   * @throws lang.IllegalArgumentException
   */
  public function set(Generic $instance= null, $value) {
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

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'($'.$this->name().')';
  }
}