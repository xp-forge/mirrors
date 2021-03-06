<?php namespace lang\mirrors;

use lang\ElementNotFoundException;

/**
 * A type's fields 
 *
 * @test  xp://lang.mirrors.unittest.TypeMirrorFieldsTest
 */
class Fields extends Members {

  /**
   * Checks whether a given field is provided
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return 0 === strncmp($name, '__', 2) ? false : $this->mirror->reflect->hasField($name);
  }

  /**
   * Returns a given field if provided or raises an exception
   *
   * @param  string $name
   * @return lang.mirrors.Field
   * @throws lang.ElementNotFoundException
   */
  public function named($name) {
    if ($this->provides($name)) {
      return new Field($this->mirror, $this->mirror->reflect->fieldNamed($name));
    }
    throw new ElementNotFoundException('No field $'.$name.' in '.$this->mirror->name());
  }

  /**
   * Iterates over fields.
   *
   * @param  util.Filter $filter
   * @return iterable
   */
  public function all($filter= null) {
    foreach ($this->mirror->reflect->allFields() as $name => $member) {
      if (0 === strncmp('__', $name, 2)) continue;
      $field= new Field($this->mirror, $member);
      if (null === $filter || $filter->accept($field)) yield $field;
    }
  }

  /**
   * Iterates over declared fields.
   *
   * @param  util.Filter $filter
   * @return iterable
   */
  public function declared($filter= null) {
    foreach ($this->mirror->reflect->declaredFields() as $name => $member) {
      if (0 === strncmp('__', $name, 2)) continue;
      $field= new Field($this->mirror, $member);
      if (null === $filter || $filter->accept($field)) yield $field;
    }
  }

  /**
   * Iterates over fields.
   *
   * @deprecated Use all() or declared() instead
   * @param  int $kind Either Member::$STATIC or Member::$INSTANCE bitwise-or'ed with Member::$DECLARED
   * @return iterable
   */
  public function of($kind) {
    $instance= ($kind & Member::$STATIC) === 0;
    $fields= ($kind & Member::$DECLARED)
      ? $this->mirror->reflect->declaredFields()
      : $this->mirror->reflect->allFields()
    ;
    foreach ($fields as $name => $field) {
      if (0 === strncmp('__', $name, 2) || $instance === $field['access']->isStatic()) continue;
      yield new Field($this->mirror, $field);
    }
  }
}