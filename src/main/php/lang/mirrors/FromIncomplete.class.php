<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\Type;
use lang\XPClass;
use lang\ElementNotFoundException;

class FromIncomplete extends \lang\Object implements Source {
  public $name;

  public function __construct($name) {
    $this->name= $name;
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() { return null; }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() {
    $sep= strrpos($this->name, '\\');
    return false === $ns ? $this->name : substr($this->name, 0, $sep + 1);
  }

  /** @return string */
  public function packageName() {
    $sep= strrpos($this->name, '\\');
    return false === $ns ? $this->name : substr($this->name, $sep + 1);
  }

  /** @return self */
  public function typeParent() { return null; }

  /** @return string */
  public function typeComment() { return null; }

  /** @return var */
  public function typeAnnotations() { return []; }

  /** @return lang.mirrors.Modifiers */
  public function typeModifiers() { return new Modifiers(Modifiers::IS_PUBLIC); }

  /** @return lang.mirrors.Kind */
  public function typeKind() { return Kind::$CLASS; }

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  string $class
   * @return bool
   */
  public function isSubtypeOf($class) { return false; }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeImplements($name) { return false; }

  /** @return php.Generator */
  public function allInterfaces() { yield; }

  /** @return php.Generator */
  public function declaredInterfaces() { yield; }

  /** @return php.Generator */
  public function allTraits() { yield; }

  /** @return php.Generator */
  public function declaredTraits() { yield; }

  /**
   * Returns whether this type uses a given trait
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name) { return false; }

  /** @return [:var] */
  public function constructor() {
    return [
      'name'    => '__default',
      'access'  => Modifiers::IS_PUBLIC,
      'holder'  => $this->name,
      'comment' => function() { return null; },
      'params'  => function() { return []; },
      'value'   => null
    ];
  }

  /**
   * Creates a new instance
   *
   * @param  var[] $args
   * @return lang.Generic
   */
  public function newInstance($args) {
    throw new IllegalArgumentException('Cannot instantiate incomplete type '.$this->name);
  }

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name) { return false; }

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name) {
    throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allFields() { yield; }

  /** @return php.Generator */
  public function declaredFields() { yield; }

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name) { return false; }

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function methodNamed($name) {
    throw new ElementNotFoundException('No method named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allMethods() { yield; }

  /** @return php.Generator */
  public function declaredMethods() { yield; }

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name) { return false; }

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function constantNamed($name) {
    throw new ElementNotFoundException('No constant named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allConstants() { yield; }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    return $name;
  }

  /**
   * Returns whether a given value is equal to this reflection source
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}