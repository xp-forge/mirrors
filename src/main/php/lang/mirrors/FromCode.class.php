<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromCode extends \lang\Object implements Source {
  private $unit, $decl;

  public function __construct($name) {
    $this->unit= (new ClassSyntax())->parse(new ClassSource(strtr($name, '\\', '.')));
    $this->decl= $this->unit->declaration();
  }

  /** @return string */
  public function typeName() { 
    $package= $this->unit->package();
    return ($package ? $package.'.' : '').$this->decl['name'];
  }

  /** @return string */
  public function typeDeclaration() { return $this->decl['name']; }

  /** @return string */
  public function packageName() { return $this->unit->package(); }

  /** @return string */
  public function typeParent() {
    $parent= $this->decl['parent'];
    return $parent ? new self($this->resolve($parent)) : null;
  }

  /** @return var */
  public function typeAnnotations() { return $this->decl['annotations']; }

  /** @return lang.mirrors.Modifiers */
  public function typeModifiers() {
    if ('trait' === $this->decl['kind']) {
      return new Modifiers(Modifiers::IS_PUBLIC | Modifiers::IS_ABSTRACT);
    } else {
      return new Modifiers(array_merge(['public'], $this->decl['modifiers']));
    }
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