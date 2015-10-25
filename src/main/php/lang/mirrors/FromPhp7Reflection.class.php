<?php namespace lang\mirrors;

use lang\Type;
use lang\XPClass;

class FromPhp7Reflection extends FromReflection {

  static function __static() { }

  /**
   * Maps reflection type
   *
   * @param  php.ReflectionMethod|php.ReflectionParameter $reflect
   * @param  string $name
   * @return php.Closure
   */
  private function mapReflectionType($reflect, $name) {
    if ('self' === $name) {
      return function() use($reflect) { return new XPClass($reflect->getDeclaringClass()); };
    } else if ('parent' === $name) {
      return function() use($reflect) { return new XPClass($reflect->getDeclaringClass()->getParentClass()); };
    } else {
      return function() use($name) { return Type::forName($name); };
    }
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  protected function param($pos, $reflect) {
    if ($type= $reflect->getType()) {
      $type= $this->mapReflectionType($reflect, (string)$type);
    } else {
      $type= null;
    }

    if ($var= $reflect->isVariadic()) {
      $default= null;
    } else if ($reflect->isOptional()) {
      $default= function() use($reflect) { return $reflect->getDefaultValue(); };
    } else {
      $default= null;
    }

    return [
      'pos'         => $pos,
      'name'        => $reflect->name,
      'type'        => $type,
      'ref'         => $reflect->isPassedByReference(),
      'default'     => $default,
      'var'         => $var,
      'annotations' => function() use($reflect) { return $this->paramAnnotations($reflect); }
    ];
  }

  /**
   * Maps a method
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  protected function method($reflect) {
    $method= parent::method($reflect);
    if ($type= $reflect->getReturnType()) {
      $method['returns']= $this->mapReflectionType($reflect, (string)$type);
    }
    return $method;
  }
}