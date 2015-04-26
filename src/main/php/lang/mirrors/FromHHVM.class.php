<?php namespace lang\mirrors;

use lang\mirrors\parse\Value;
use lang\Type;

class FromHHVM extends FromReflection {

  /**
   * Maps a Hack type to an XP type
   *
   * @param  string $type
   * @return string
   */
  private function mapType($type) {
    if ('self' === $type) {
      return strtr($this->reflect->name, '\\', '.');
    } else if ('parent' === $type) {
      return strtr($this->reflect->getParentClass()->name, '\\', '.');
    } else if (0 === strncmp($type, 'array', 5)) {
      $components= explode(',', substr($type, 6, -1));
      if (2 === sizeof($components)) {
        return '[:'.$this->mapType(trim($components[1])).']';
      } else {
        return $this->mapType($components[0]).'[]';
      }
    }
    return strtr($type, ['HH\\' => '']);
  }

  /** @return var */
  public function typeAnnotations() {
    $annotations= [];
    foreach ($this->reflect->getAttributes() as $name => $value) {
      $annotations[$name]= empty($value) ? null : new Value($value[0]);
    }

    return empty($annotations) ? parent::typeAnnotations() : $annotations;
  }

  /**
   * Maps a field
   *
   * @param  php.ReflectionProperty $reflect
   * @return [:var]
   */
  protected function field($reflect) {
    $field= parent::field($reflect);
    if ($type= $reflect->getTypeText()) {
      $field['type']= $this->mapType($type);
    }
    return $field;
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  protected function param($pos, $reflect) {
    $hint= $reflect->info['type_hint'];
    if ('' === $hint) {
      $type= null;
    } else if ('array' === $hint) {
      $type= function() { return Type::$ARRAY; };
    } else if ('callable' === $hint) {
      $type= function() { return Type::$CALLABLE; };
    } else {
      $type= function() use ($hint) { return Type::forName($this->mapType($hint)); };
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
   * Maps annotations
   *
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  protected function paramAnnotations($reflect) {
    $annotations= [];
    foreach ($reflect->getAttributes() as $name => $value) {
      $annotations[$name]= empty($value) ? null : new Value($value[0]);
    }

    return empty($annotations) ? parent::paramAnnotations($reflect) : $annotations;
  }

  /**
   * Maps a method
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  protected function method($reflect) {
    $method= parent::method($reflect);
    if ($type= $reflect->getReturnTypeText()) {
      $method['returns']= $this->mapType($type);
    }
    return $method;
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
      $annotations[$name]= empty($value) ? null : new Value($value[0]);
    }

    return empty($annotations) ? parent::methodAnnotations($reflect) : $annotations;
  }
}