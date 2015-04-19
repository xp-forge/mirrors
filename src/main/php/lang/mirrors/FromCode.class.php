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

  /**
   * Fetches all declarations
   *
   * @param  bool $parent Whether to include parents
   * @param  bool $traits Whether to include traits
   * @return [:var]
   */
  private function declarations($parent, $traits) {
    yield $this->decl;

    if ($parent) {
      $decl= $this->decl;
      while ($decl= $this->declarationOf($decl['parent'])) {
        yield $decl;
      }
    }

    if ($traits && isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        yield $this->declarationOf($trait);
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
    foreach ($this->declarations(true, false) as $decl) {
      if ($class === $this->resolve0($decl['parent'])) return true;
      foreach ((array)$decl['implements'] as $interface) {
        if ($class === $this->resolve0($interface)) return true;
      }
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
    foreach ($this->declarations(true, false) as $decl) {
      foreach ((array)$decl['implements'] as $interface) {
        if ($name === $this->resolve0($interface)) return true;
      }
    }
    return false;
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

  /** @return php.Generator */
  public function allTraits() {
    $decl= $this->decl;
    do {
      if (!isset($decl['use'])) continue;
      foreach ($decl['use'] as $trait => $definition) {
        if ('\__xp' === $trait) continue;
        $name= $this->resolve0($trait);
        yield $name => new self($name);
      }
    } while ($decl= $this->declarationOf($decl['parent']));
  }

  /** @return php.Generator */
  public function declaredTraits() {
    if (!isset($this->decl['use'])) return;
    foreach ($this->decl['use'] as $trait => $definition) {
      if ('\__xp' === $trait) continue;
      $name= $this->resolve0($trait);
      yield $name => new self($name);
    }
  }

  /**
   * Returns whether this type uses a given trait
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name) {
    foreach ($this->declarations(true, false) as $decl) {
      if (!isset($decl['use'])) continue;
      foreach ($decl['use'] as $trait => $definition) {
        if ($name === $this->resolve0($trait)) return true;
      }
    }
    return false;
  }

  /** @return [:var] */
  public function constructor() {
    foreach ($this->declarations(true, true) as $decl) {
      if (isset($decl['method']['__construct'])) return $decl['method']['__construct'];
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
    foreach ($this->declarations(true, true) as $decl) {
      if (isset($decl['field'][$name])) return true;
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
    foreach ($this->declarations(true, true) as $decl) {
      if (isset($decl['field'][$name])) return $decl['field'][$name];
    }
    throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allFields() {
    foreach ($this->declarations(true, true) as $decl) {
      foreach ($decl['field'] as $name => $field) {
        yield $name => $field;
      }
    }
  }

  /** @return php.Generator */
  public function declaredFields() {
    foreach ($this->declarations(false, true) as $decl) {
      foreach ($decl['field'] as $name => $field) {
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
    foreach ($this->declarations(true, true) as $decl) {
      if (isset($decl['method'][$name])) return true;
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
    foreach ($this->declarations(true, true) as $decl) {
      if (isset($decl['method'][$name])) return $this->method($decl['name'], $decl['method'][$name]);
    }
    throw new ElementNotFoundException('No method named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allMethods() {
    foreach ($this->declarations(true, true) as $decl) {
      foreach ($decl['method'] as $name => $method) {
        yield $name => $this->method($decl['name'], $method);
      }
    }
  }

  /** @return php.Generator */
  public function declaredMethods() {
    foreach ($this->declarations(false, true) as $decl) {
      foreach ($decl['method'] as $name => $method) {
        yield $name => $this->method($decl['name'], $method);
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
    foreach ($this->declarations(true, false) as $decl) {
      if (isset($decl['const'][$name])) return true;
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
    foreach ($this->declarations(true, false) as $decl) {
      if (isset($decl['const'][$name])) return $decl['const'][$name]['value']->resolve($this->mirror);
    }
    throw new ElementNotFoundException('No constant named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allConstants() {
    foreach ($this->declarations(true, false) as $decl) {
      foreach ($decl['const'] as $name => $const) {
        yield $name => $const['value']->resolve($this->mirror);
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
    return new self($this->resolve0($name));
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