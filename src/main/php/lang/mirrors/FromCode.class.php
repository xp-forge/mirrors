<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\Type;
use lang\XPClass;
use lang\ElementNotFoundException;

class FromCode extends \lang\Object implements Source {
  private static $syntax;
  private $unit, $decl;
  public $name;

  static function __static() {
    self::$syntax= new ClassSyntax();
  }

  public function __construct($name, Sources $source= null) {
    $this->unit= self::$syntax->codeUnitOf($name);
    $this->decl= $this->unit->declaration();
    $package= $this->unit->package();
    $this->name= ($package ? $package.'\\' : '').$this->decl['name'];
    $this->source= $source ?: Sources::$CODE;
  }

  /**
   * Fetches all types to merge with
   *
   * @param  bool $parent Whether to include parents
   * @param  bool $traits Whether to include traits
   * @return php.Generator
   */
  private function merge($parent, $traits) {
    if ($parent && isset($this->decl['parent'])) {
      yield $this->resolve($this->decl['parent']);
    }

    if ($traits && isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        yield $this->resolve($trait);
      }
    }
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() { return $this->unit; }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() { return $this->decl['name']; }

  /** @return string */
  public function packageName() { return strtr($this->unit->package(), '\\', '.'); }

  /** @return self */
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

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  string $class
   * @return bool
   */
  public function isSubtypeOf($class) {
    if ($class === $this->resolve0($this->decl['parent'])) return true;
    foreach ((array)$this->decl['implements'] as $interface) {
      if ($class === $this->resolve0($interface)) return true;
    }
    foreach ($this->merge(true, false) as $reflect) {
      if ($reflect->isSubtypeOf($class)) return true;
    }
    return false;
  }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeImplements($name) {
    foreach ($this->decl['implements'] as $interface) {
      if ($name === $this->resolve0($interface)) return true;
    }
    foreach ($this->merge(true, false) as $reflect) {
      if ($reflect->typeImplements($name)) return true;
    }
    return false;
  }

  /** @return php.Generator */
  public function allInterfaces() {
    foreach ($this->decl['implements'] as $interface) {
      if ('\__xp' === $interface) continue;
      $name= $this->resolve0($interface);
      yield $name => $this->source->reflect($name);
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allInterfaces($name) as $name => $reflect) {
        yield $name => $reflect;
      }
    }
  }

  /** @return php.Generator */
  public function declaredInterfaces() {
    foreach ($this->decl['implements'] as $interface) {
      if ('\__xp' === $interface) continue;
      $name= $this->resolve0($interface);
      yield $name => $this->source->reflect($name);
    }
  }

  /** @return php.Generator */
  public function allTraits() {
    if (isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        $name= $this->resolve0($trait);
        yield $name => $this->source->reflect($name);
      }
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allTraits($name) as $name => $reflect) {
        yield $name => $reflect;
      }
    }
  }

  /** @return php.Generator */
  public function declaredTraits() {
    if (isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        $name= $this->resolve0($trait);
        yield $name => $this->source->reflect($name);
      }
    }
  }

  /**
   * Returns whether this type uses a given trait
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name) {
    if (isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        if ($name === $this->resolve0($trait)) return true;
      }
    }
    foreach ($this->merge(true, false) as $reflect) {
      if ($reflect->typeUses($name)) return true;
    }
    return false;
  }

  /** @return [:var] */
  public function constructor() {
    if (isset($this->decl['method']['__construct'])) {
      return $this->method($this->decl['name'], $this->decl['method']['__construct']);
    }
    foreach ($this->merge(true, true) as $reflect) {
      if ($reflect->hasMethod('__construct')) return $reflect->constructor();
    }

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

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name) {
    if (isset($this->decl['field'][$name])) return true;
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $cmp => $field) {
        if ($cmp === $name) return true;
      }
    }

    return false;
  }

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name) {
    if (isset($this->decl['field'][$name])) return $this->decl['field'][$name];
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $cmp => $field) {
        if ($cmp === $name) return $field;
      }
    }

    throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allFields() {
    foreach ($this->decl['field'] as $name => $field) {
      yield $name => $field;
    }
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $name => $field) {
        if (isset($this->decl['field'][$name])) continue;
        yield $name => $field;
      }
    }
  }

  /** @return php.Generator */
  public function declaredFields() {
    foreach ($this->decl['field'] as $name => $field) {
      yield $name => $field;
    }
    foreach ($this->merge(false, true) as $reflect) {
      foreach ($reflect->allFields() as $name => $field) {
        if (isset($this->decl['field'][$name])) continue;
        yield $name => $field;
      }
    }
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  [:var] $param
   * @return [:var]
   */
  private function param($pos, $param) {
    if ($param['default']) {
      $default= function() use($param) { return $param['default']->resolve($this->mirror); };
    } else {
      $default= null;
    }

    if ('array' === $param['type']) {
      $type= function() { return Type::$ARRAY; };
    } else if ('callable' === $param['type']) {
      $type= function() { return Type::$CALLABLE; };
    } else if ($param['type']) {
      $type= function() use($param) { return new XPClass($this->resolve0($param['type'])); };
    } else {
      $type= null;
    }

    return [
      'pos'     => $pos,
      'name'    => $param['name'],
      'type'    => $type,
      'ref'     => $param['ref'],
      'var'     => $param['var'],
      'default' => $default
    ];
  }

  /**
   * Maps a method
   *
   * @param  string $holder
   * @param  [:var] $method
   * @return [:var]
   */
  private function method($holder, $method) {
    return [
      'name'    => $method['name'],
      'access'  => $method['access'],
      'holder'  => $holder,
      'params'  => function() use($method) {
        $params= [];
        foreach ($method['params'] as $pos => $param) {
          $params[]= $this->param($pos, $param);
        }
        return $params;
      },
      'comment' => function() use($method) { return $method['comment']; },
      'value'   => null
    ];
  }

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name) {
    if (isset($this->decl['method'][$name])) return true;
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allMethods() as $cmp => $field) {
        if ($cmp === $name) return true;
      }
    }

    return false;
  }

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function methodNamed($name) {
    if (isset($this->decl['method'][$name])) return $this->method($this->decl['name'], $this->decl['method'][$name]);
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allMethods() as $cmp => $method) {
        if ($cmp === $name) return $method;
      }
    }

    throw new ElementNotFoundException('No method named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allMethods() {
    foreach ($this->decl['method'] as $name => $method) {
      yield $name => $this->method($this->decl['name'], $method);
    }
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allMethods() as $name => $method) {
        if (isset($this->decl['method'][$name])) continue;
        yield $name => $method;
      }
    }
  }

  /** @return php.Generator */
  public function declaredMethods() {
    foreach ($this->decl['method'] as $name => $method) {
      yield $name => $this->method($this->decl['name'], $method);
    }
    foreach ($this->merge(false, true) as $reflect) {
      foreach ($reflect->allMethods() as $name => $method) {
        if (isset($this->decl['method'][$name])) continue;
        yield $name => $method;
      }
    }
  }

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name) {
    if (isset($this->decl['const'][$name])) return true;
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allFields() as $cmp => $const) {
        if ($cmp === $name) return true;
      }
    }

    return false;
  }

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function constantNamed($name) {
    if (isset($this->decl['const'][$name])) return $this->decl['const'][$name]['value']->resolve($this->mirror);
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $cmp => $const) {
        if ($cmp === $name) return $const;
      }
    }

    throw new ElementNotFoundException('No constant named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allConstants() {
    foreach ($this->decl['const'] as $name => $const) {
      yield $name => $const['value']->resolve($this->mirror);
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allConstants() as $name => $const) {
        if (isset($this->decl['const'][$name])) continue;
        yield $name => $const;
      }
    }
  }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return string
   */
  private function resolve0($name) {
    if ('self' === $name || $name === $this->decl['name']) {
      return $this->name;
    } else if ('parent' === $name) {
      return $this->resolve0($this->decl['parent']);
    } else if ('\\' === $name{0}) {
      return substr($name, 1);
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return strtr($name, '.', '\\');
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
    return $this->source->reflect($this->resolve0($name));
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