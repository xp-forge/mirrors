<?php namespace lang\mirrors;

class FromReflection extends \lang\Object implements Source {
  private $reflect;
  public $name;

  public function __construct(\ReflectionClass $reflect) {
    $this->reflect= $reflect;
    $this->name= $reflect->getName();
  }

  public function typeName() { return strtr($this->name, '\\', '.'); }

  public function typeParent() {
    $parent= $this->reflect->getParentClass();
    return $parent ? new self($parent) : null;
  }

  public function __call($name, $args) {
    return $this->reflect->{$name}(...$args);
  }
}