<?php namespace lang\mirrors;

use lang\mirrors\parse\Value;

class FromHHVMReflection extends FromReflection {
  private $types;

  static function __static() { }

  /**
   * Creates a new HHVM reflection source
   *
   * @param  php.ReflectionClass $reflect
   * @param  lang.mirrors.Sources $source
   */
  public function __construct(\ReflectionClass $reflect, Sources $source= null) {
    parent::__construct($reflect, $source);
    $this->types= new HackTypes($reflect);
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
      $field['type']= function() use($type) { return $this->types->map($type); };
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
    if ($reflect->isVariadic()) {
      $var= true;
      $default= null;

      // Types for variadics are hard-wired to `array` in HHVM, this is inconsistent with
      // PHP, which returns the type without an array component. Drop the type information 
      // alltogether, which will default back to using the types in `@param`, or `var`.
      $type= null;
    } else {
      $hint= $reflect->getTypeText();
      if ('' === $hint) {
        $type= null;
      } else {
        $type= function() use ($hint) { return $this->types->map($hint); };
      }

      if ($reflect->isOptional()) {
        $var= null;
        $default= function() use($reflect) { return $reflect->getDefaultValue(); };
      } else {
        $var= false;
        $default= null;
      }
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
      $method['returns']= function() use($type) { return $this->types->map($type); };
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