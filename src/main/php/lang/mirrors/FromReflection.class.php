<?php namespace lang\mirrors;

class FromReflection extends \lang\Object implements Source {
  private $reflect;
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


  public function __call($name, $args) {
    return $this->reflect->{$name}(...$args);
  }

  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}