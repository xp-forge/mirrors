<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;

class FromCode extends \lang\Object implements Source {
  private static $syntax;
  private $unit, $decl;
  public $name;

  static function __static() {
    self::$syntax= new ClassSyntax();
  }

  public function __construct($name) {
    $this->unit= self::$syntax->codeUnitOf($name);
    $this->decl= $this->unit->declaration();
    $package= $this->unit->package();
    $this->name= ($package ? $package.'\\' : '').$this->decl['name'];
  }

  /**
   * Fetches class declaration
   *
   * @param  string $name
   * @return [:var]
   */
  private function declarationOf($name) {
    return $name ? self::$syntax->codeUnitOf($this->resolve0($name))->declaration() : null;
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() { return $this->unit; }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() { return $this->decl['name']; }

  /** @return string */
  public function packageName() { return strtr($this->unit->package(), '\\', '.'); }

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
    } else if ('lang\Enum' === $this->resolve0($this->decl['parent'])) {
      return Kind::$ENUM;
    } else {
      return Kind::$CLASS;
    }
  }

  /** @return php.Generator */
  public function allInterfaces() {
    $decl= $this->decl;
    do {
      foreach ($decl['implements'] as $interface) {
        $name= $this->resolve0($interface);
        yield strtr($name, '.', '\\') => new self($name);
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /** @return php.Generator */
  public function declaredInterfaces() {
    foreach ($this->decl['implements'] as $interface) {
      $name= $this->resolve0($interface);
      yield strtr($name, '.', '\\') => new self($name);
    }
  }

  public function typeImplements($name) {
    $decl= $this->decl;
    do {
      foreach ($decl['implements'] as $interface) {
        if ($name === $this->resolve0($interface)) return true;
      }
    } while ($decl= $this->declarationOf($decl['parent']));
    return false;
  }

  /** @return php.Generator */
  public function allTraits() {
    $decl= $this->decl;
    do {
      if (!isset($decl['use'])) continue;
      foreach ($decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        $name= $this->resolve0($trait);
        yield strtr($name, '.', '\\') => new self($name);
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /** @return php.Generator */
  public function declaredTraits() {
    if (!isset($this->decl['use'])) return;
    foreach ($this->decl['use'] as $trait => $definition) {
      if ('\__xp' === $trait) continue;
      $name= $this->resolve0($trait);
      yield strtr($name, '.', '\\') => new self($name);
    }
  }

  public function typeUses($name) {
    $decl= $this->decl;
    do {
      if (!isset($decl['use'])) continue;
      foreach ($decl['use'] as $trait => $definition) {
        if ($name === $this->resolve0($trait)) return true;
      }
    } while ($decl= $this->declarationOf($decl['parent']));
    return false;
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
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   */
  public function constantNamed($name) {
    $decl= $this->decl;
    do {
      if (isset($decl['const'][$name])) return $decl['const'][$name]['value']->resolve($this->mirror);
    } while ($decl= $this->declarationOf($decl['parent']));
    return null;
  }

  /** @return php.Generator */
  public function allConstants() {
    $decl= $this->decl;
    do {
      if (!isset($decl['const'])) continue;
      foreach ($decl['const'] as $name => $const) {
        yield $name => $const['value']->resolve($this->mirror);
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  string $class
   * @return bool
   */
  public function isSubtypeOf($class) {
    $decl= $this->decl;
    do {
      if ($class === $this->resolve0($decl['parent'])) return true;
      foreach ((array)$decl['implements'] as $interface) {
        if ($class === $this->resolve0($interface)) return true;
      }
    } while ($decl= $this->declarationOf($decl['parent']));
    return false;
  }

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
      return substr($name, 1);
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return strtr($name, '\\', '.');
    } else {
      foreach ($this->unit->imports() as $imported) {
        if (0 === substr_compare($imported, $name, strrpos($imported, '\\') + 1)) return $imported;
      }
      $package= $this->unit->package();
      return ($package ? $package.'\\' : '').$name;
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