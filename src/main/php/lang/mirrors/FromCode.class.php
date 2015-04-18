<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromCode extends \lang\Object implements Source {
  private $unit, $decl;
  private static $syntax;

  static function __static() {
    self::$syntax= new ClassSyntax();
  }

  public function __construct($name) {
    $this->unit= self::$syntax->parse(new ClassSource(strtr($name, '\\', '.')));
    $this->decl= $this->unit->declaration();
  }

  /**
   * Fetches class declaration
   *
   * @param  string $class
   * @return [:var]
   */
  private function declarationOf($class) {
    return $class ? self::$syntax->parse(new ClassSource($this->resolve0($class)))->declaration() : null;
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() { return $this->unit; }

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
    return $parent ? $this->resolve($parent) : null;
  }

  /** @return string */
  public function typeComment() { return $this->decl['comment']; }

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

  /** @return lang.mirrors.Kind */
  public function typeKind() {
    if ('trait' === $this->decl['kind']) {
      return Kind::$TRAIT;
    } else if ('interface' === $this->decl['kind']) {
      return Kind::$INTERFACE;
    } else if ('lang.Enum' === $this->resolve0($this->decl['parent'])) {
      return Kind::$ENUM;
    } else {
      return Kind::$CLASS;
    }
  }

  /** @return [:var] */
  public function constructor() {
    $decl= $this->decl;
    do {
      if (isset($decl['method']['__construct'])) return $decl['method']['__construct'];
    } while ($decl= $this->declarationOf($decl['parent']));

    return [
      'name'    => '__default',
      'access'  => Modifiers::IS_PUBLIC,
      'holder'  => $this->decl['name'],
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
    throw new IllegalArgumentException('Verifying '.$this->name.': Cannot instantiate');
  }

  /** @return bool */
  public function hasField($name) {
    $decl= $this->decl;
    do {
      if (isset($decl['field'][$name])) return true;
    } while ($decl= $this->declarationOf($decl['parent']));
    return false;
  }

  /** @return [:var] */
  public function fieldNamed($name) {
    $decl= $this->decl;
    do {
      if (isset($decl['field'][$name])) return $decl['field'][$name];
    } while ($decl= $this->declarationOf($decl['parent']));
    return null;
  }

  /** @return php.Generator */
  public function allFields() {
    $decl= $this->decl;
    do {
      foreach ($decl['field'] as $name => $field) {
        yield $name => $field;
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /** @return php.Generator */
  public function declaredFields() {
    foreach ($this->decl['field'] as $name => $field) {
      yield $name => $field;
    }
  }

  /** @return bool */
  public function hasMethod($name) { return isset($this->decl['method'][$name]); }

  /** @return [:var] */
  public function methodNamed($name) {
    $decl= $this->decl;
    do {
      if (isset($decl['method'][$name])) return $decl['method'][$name];
    } while ($decl= $this->declarationOf($decl['parent']));
    return null;
  }

  /** @return php.Generator */
  public function allMethods() {
    $decl= $this->decl;
    do {
      foreach ($decl['method'] as $name => $method) {
        yield $name => $method;
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /** @return php.Generator */
  public function declaredMethods() {
    foreach ($this->decl['method'] as $name => $method) {
      yield $name => $method;
    }
  }

  /** @return bool */
  public function hasConstant($name) { return isset($this->decl['const'][$name]); }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return string
   */
  private function resolve0($name) {
    if ('self' === $name || $name === $this->decl['name']) {
      return $this->typeName();
    } else if ('parent' === $name) {
      return $this->resolve0($this->decl['parent']);
    } else if ('\\' === $name{0}) {
      return strtr(substr($name, 1), '\\', '.');
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return strtr($name, '\\', '.');
    } else {
      foreach ($this->unit->imports() as $imported) {
        if (0 === substr_compare($imported, $name, strrpos($imported, '.') + 1)) return $imported;
      }
      return $this->unit->package().'.'.$name;
    }
  }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    return new self($this->resolve0($name));
  }

  public function equals($cmp) {
    return $cmp instanceof self && $this->typeName() === $cmp->typeName();
  }
}