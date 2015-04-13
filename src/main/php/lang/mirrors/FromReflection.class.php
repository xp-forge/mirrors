<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromReflection extends \lang\Object implements Source {
  private $reflect;
  private $unit= null;
  public $name;

  public function __construct(\ReflectionClass $reflect) {
    $this->reflect= $reflect;
    $this->name= $reflect->getName();
  }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() { return $this->reflect->getShortName(); }

  /** @return string */
  public function packageName() { return strtr($this->reflect->getNamespaceName(), '\\', '.'); }

  public function typeParent() {
    $parent= $this->reflect->getParentClass();
    return $parent ? new self($parent) : null;
  }

  public function unit() {
    if (null === $this->unit) {
      $this->unit= (new ClassSyntax())->parse(new ClassSource($this->typeName()));
    }
    return $this->unit;
  }

  /** @return var */
  public function typeAnnotations() {
    return $this->unit()->declaration()['annotations'];
  }

  /** @return lang.mirrors.Modifiers */
  public function typeModifiers() {

    // HHVM and PHP differ in this. We'll handle traits as *always* abstract (needs
    // to be implemented) and *never* final (couldn't be implemented otherwise).
    if ($this->reflect->isTrait()) {
      return new Modifiers(Modifiers::IS_PUBLIC | Modifiers::IS_ABSTRACT);
    } else {
      $r= Modifiers::IS_PUBLIC;
      $m= $this->reflect->getModifiers();
      $m & \ReflectionClass::IS_EXPLICIT_ABSTRACT && $r |= Modifiers::IS_ABSTRACT;
      $m & \ReflectionClass::IS_IMPLICIT_ABSTRACT && $r |= Modifiers::IS_ABSTRACT;
      $m & \ReflectionClass::IS_FINAL && $r |= Modifiers::IS_FINAL;
      return new Modifiers($r);
    }
  }

  public function __call($name, $args) {
    return $this->reflect->{$name}(...$args);
  }

  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}