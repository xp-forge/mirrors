<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\Type;
use lang\XPClass;
use lang\Primitive;
use lang\ElementNotFoundException;

class FromCode extends \lang\Object implements Source {
  private static $syntax;
  private $unit;
  protected $decl;
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

  /** @return bool */
  public function present() { return true; }

  /**
   * Fetches all types to merge with
   *
   * @param  bool $parent Whether to include parents
   * @param  bool $traits Whether to include traits
   * @return php.Generator
   */
  private function merge($parent, $traits) {
    $return= [];
    if ($parent && isset($this->decl['parent'])) {
      $return[]= $this->resolve($this->decl['parent']);
    }

    if ($traits && isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        $return[]= $this->resolve($trait);
      }
    }
    return new \ArrayIterator($return);
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
  public function typeAnnotations() { return $this->decl['annotations'][null]; }

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
    $return= [];
    foreach ($this->decl['implements'] as $interface) {
      $name= $this->resolve0($interface);
      $return[$name]= $this->source->reflect($name);
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allInterfaces($name) as $name => $reflect) {
        $return[$name]= $reflect;
      }
    }
    return new \ArrayIterator($return);
  }

  /** @return php.Generator */
  public function declaredInterfaces() {
    $return= [];
    foreach ($this->decl['implements'] as $interface) {
      $name= $this->resolve0($interface);
      $return[$name]= $this->source->reflect($name);
    }
    return new \ArrayIterator($return);
  }

  /** @return php.Generator */
  public function allTraits() {
    if (isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        $name= $this->resolve0($trait);
        $return[$name]= $this->source->reflect($name);
      }
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allTraits($name) as $name => $reflect) {
        $return[$name]= $reflect;
      }
    }
    return new \ArrayIterator($return);
  }

  /** @return php.Generator */
  public function declaredTraits() {
    $return= [];
    if (isset($this->decl['use'])) {
      foreach ($this->decl['use'] as $trait => $definition) {
        $name= $this->resolve0($trait);
        $return[$name]= $this->source->reflect($name);
      }
    }
    return new \ArrayIterator($return);
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
      return $this->method($this->name, $this->decl['method']['__construct']);
    }
    foreach ($this->merge(true, true) as $reflect) {
      if ($reflect->hasMethod('__construct')) return $reflect->constructor();
    }

    return [
      'name'        => '__default',
      'access'      => new Modifiers(Modifiers::IS_PUBLIC),
      'holder'      => $this->name,
      'comment'     => function() { return null; },
      'annotations' => function() { return []; },
      'params'      => function() { return []; }
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
   * Map type
   *
   * @param  lang.mirrors.parse.Resolveable $ref
   * @return function(): lang.Type
   */
  private function type($ref) {
    return function() use($ref) { return $ref->resolve($this); };
  }

  /**
   * Maps a field
   *
   * @param  string $holder
   * @param  [:var] $field
   * @return [:var]
   */
  protected function field($holder, $field) {
    return [
      'name'        => $field['name'],
      'type'        => isset($field['type']) ? $this->type($field['type']) : null,
      'access'      => new Modifiers($field['access']),
      'holder'      => $holder,
      'annotations' => function() use($field) { return $field['annotations'][null]; },
      'comment'     => function() use($field) { return $field['comment']; }
    ];
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
    if (isset($this->decl['field'][$name])) return $this->field($this->name, $this->decl['field'][$name]);
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $cmp => $field) {
        if ($cmp === $name) return $field;
      }
    }

    throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allFields() {
    $return= [];
    if (isset($this->decl['field'])) {
      foreach ($this->decl['field'] as $name => $field) {
        $return[$name]= $this->field($this->name, $field);
      }
    }
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allFields() as $name => $field) {
        if (isset($this->decl['field'][$name])) continue;
        $return[$name]= $field;
      }
    }
    return new \ArrayIterator($return);
  }

  /** @return php.Generator */
  public function declaredFields() {
    $return= [];
    if (isset($this->decl['field'])) {
      foreach ($this->decl['field'] as $name => $field) {
        $return[$name]= $this->field($this->name, $field);
      }
    }
    foreach ($this->merge(false, true) as $reflect) {
      foreach ($reflect->allFields() as $name => $field) {
        if (isset($this->decl['field'][$name])) continue;
        $return[$name]= $field;
      }
    }
    return new \ArrayIterator($return);
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  [:var] $param
   * @param  [:var] $annotations
   * @return [:var]
   */
  protected function param($pos, $param, $annotations) {
    if ($param['default']) {
      $default= function() use($param) { return $param['default']->resolve($this->mirror); };
    } else {
      $default= null;
    }

    return [
      'pos'         => $pos,
      'name'        => $param['name'],
      'type'        => isset($param['type']) ? $this->type($param['type']) : null,
      'ref'         => $param['ref'],
      'var'         => $param['var'],
      'default'     => $default,
      'annotations' => function() use($annotations) { return $annotations; }
    ];
  }

  /**
   * Maps a method
   *
   * @param  string $holder
   * @param  [:var] $method
   * @return [:var]
   */
  protected function method($holder, $method) {
    return [
      'name'        => $method['name'],
      'access'      => new Modifiers($method['access']),
      'holder'      => $holder,
      'returns'     => isset($method['returns']) ? $this->type($method['returns']) : null,
      'params'      => function() use($method) {
        $params= [];
        foreach ($method['params'] as $pos => $param) {
          $target= '$'.$param['name'];
          $params[]= $this->param($pos, $param, isset($param['annotations'])
            ? $param['annotations'][null]
            : (isset($method['annotations'][$target]) ? $method['annotations'][$target] : [])
          );
        }
        return $params;
      },
      'annotations' => function() use($method) { return $method['annotations'][null]; },
      'comment'     => function() use($method) { return $method['comment']; }
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
    if (isset($this->decl['method'][$name])) return $this->method($this->name, $this->decl['method'][$name]);
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allMethods() as $cmp => $method) {
        if ($cmp === $name) return $method;
      }
    }

    throw new ElementNotFoundException('No method named '.$name.' in '.$this->name);
  }

  /** @return php.Generator */
  public function allMethods() {
    $return= [];
    if (isset($this->decl['method'])) {
      foreach ($this->decl['method'] as $name => $method) {
        $return[$name]= $this->method($this->name, $method);
      }
    }
    foreach ($this->merge(true, true) as $reflect) {
      foreach ($reflect->allMethods() as $name => $method) {
        if (isset($this->decl['method'][$name])) continue;
        $return[$name]= $method;
      }
    }
    return new \ArrayIterator($return);
  }

  /** @return php.Generator */
  public function declaredMethods() {
    $return= [];
    if (isset($this->decl['method'])) {
      foreach ($this->decl['method'] as $name => $method) {
        $return[$name]= $this->method($this->name, $method);
      }
    }
    foreach ($this->merge(false, true) as $reflect) {
      foreach ($reflect->allMethods() as $name => $method) {
        if (isset($this->decl['method'][$name])) continue;
        $return[$name]= $method;
      }
    }
    return new \ArrayIterator($return);
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
    $return= [];
    foreach ($this->decl['const'] as $name => $const) {
      $return[$name]= $const['value']->resolve($this->mirror);
    }
    foreach ($this->merge(true, false) as $reflect) {
      foreach ($reflect->allConstants() as $name => $const) {
        if (isset($this->decl['const'][$name])) continue;
        $return[$name]= $const;
      }
    }
    return new \ArrayIterator($return);
  }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return string
   */
  protected function resolve0($name) {
    if ('self' === $name || $name === $this->decl['name']) {
      return $this->name;
    } else if ('parent' === $name) {
      return $this->resolve0($this->decl['parent']);
    } else if ('\\' === $name{0}) {
      return substr($name, 1);
    } else if (strstr($name, '\\')) {
      $package= $this->unit->package();
      return ($package ? $package.'\\' : '').$name;
    } else if (strstr($name, '.')) {
      return strtr($name, '.', '\\');
    } else {
      $imports= $this->unit->imports();
      if (isset($imports[$name])) return $imports[$name];
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
    return $cmp instanceof Source && $this->name === $cmp->name;
  }
}