<?php namespace lang\mirrors;

use lang\mirrors\parse\Value;

class FromHHVM extends FromReflection {

  /** @return var */
  public function typeAnnotations() {
    $annotations= [];
    foreach ($this->reflect->getAttributes() as $name => $value) {
      $annotations[null][$name]= empty($value) ? null : new Value($value[0]);
    }

    return empty($annotations) ? parent::typeAnnotations() : $annotations;
  }

  /**
   * Maps annotations
   *
   * @param  var $reflect
   * @param  string $member
   * @param  string $kind Either "method" or "field"
   * @return [:var]
   */
  protected function memberAnnotations($reflect, $member, $kind) {
    $annotations= [];
    if (method_exists($reflect, 'getAttributes')) {
      foreach ($reflect->getAttributes() as $name => $value) {
        $annotations[null][$name]= empty($value) ? null : new Value($value[0]);
      }
    }

    return empty($annotations) ? parent::memberAnnotations($reflect, $member, $kind) : $annotations;
  }
}