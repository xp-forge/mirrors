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
   * Maps method annotations
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  protected function methodAnnotations($reflect) {
    $annotations= [];
    foreach ($reflect->getAttributes() as $name => $value) {
      $annotations[null][$name]= empty($value) ? null : new Value($value[0]);
    }

    return empty($annotations) ? parent::methodAnnotations($reflect) : $annotations;
  }
}