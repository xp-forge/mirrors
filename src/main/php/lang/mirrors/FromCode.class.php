<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromCode extends \lang\Object implements Source {
  private $unit;

  public function __construct($name) {
    $this->unit= (new ClassSyntax())->parse(new ClassSource(strtr($name, '\\', '.')));
  }

  /** @return string */
  public function typeName() { 
    $package= $this->unit->package();
    return ($package ? $package.'.' : '').$this->unit->declaration()['name'];
  }

  /** @return string */
  public function typeDeclaration() { return $this->unit->declaration()['name']; }

  /** @return string */
  public function typeParent() {
    $parent= $this->unit->declaration()['parent'];
    return $parent ? new self($this->resolve($parent)) : null;
  }

  private function resolve($name) {
    if ('\\' === $name{0}) {
      return strtr(substr($name, 1), '\\', '.');
    }
    throw new \lang\MethodNotImplementedException($name);
  }

  public function equals($cmp) {
    return $cmp instanceof self && $this->typeName() === $cmp->typeName();
  }
}