<?php namespace lang\mirrors;

use lang\IllegalStateException;
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
      return function() use($reflect) {
        if ($parent= $reflect->getDeclaringClass()->getParentClass()) return new XPClass($parent);
        throw new IllegalStateException('Cannot resolve parent type of class without parent');
      };
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
    if ($t= $reflect->getType()) {
      $type= $this->mapReflectionType($reflect, PHP_VERSION_ID >= 70100 ? $t->getName() : $t->__toString());
    } else {
      $type= null;
    }

    if ($reflect->isVariadic()) {
      $var= true;
      $default= null;
    } else if ($reflect->isOptional()) {
      $var= null;
      $default= function() use($reflect) { return $reflect->getDefaultValue(); };
    } else {
      $var= false;
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
    if ($t= $reflect->getReturnType()) {
      $method['returns']= $this->mapReflectionType($reflect, PHP_VERSION_ID >= 70100 ? $t->getName() : $t->__toString());
    }
    return $method;
  }
}