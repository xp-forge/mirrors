<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\XPClass;
use lang\Type;
use lang\Enum;
use lang\ElementNotFoundException;
use lang\IllegalArgumentException;
use lang\Throwable;

class FromReflection extends \lang\Object implements Source {
  protected $reflect;
  private $source;
  private $unit= null;
  public $name;

  public function __construct(\ReflectionClass $reflect, Sources $source= null) {
    $this->reflect= $reflect;
    $this->name= $reflect->name;
    $this->source= $source ?: Sources::$REFLECTION;
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() {
    if (null === $this->unit) {
      $this->unit= (new ClassSyntax())->codeUnitOf($this->typeName());
    }
    return $this->unit;
  }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() { return $this->reflect->getShortName(); }

  /** @return string */
  public function packageName() { return strtr($this->reflect->getNamespaceName(), '\\', '.'); }

  /** @return self */
  public function typeParent() {
    $parent= $this->reflect->getParentClass();
    return $parent ? $this->source->reflect($parent) : null;
  }

  /** @return string */
  public function typeComment() {
    $comment= $this->reflect->getDocComment();
    return false === $comment ? null : $comment;
  }

  /** @return var */
  public function typeAnnotations() { return $this->codeUnit()->declaration()['annotations']; }

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

  /** @return lang.mirrors.Kind */
  public function typeKind() {
    if ($this->reflect->isTrait()) {
      return Kind::$TRAIT;
    } else if ($this->reflect->isInterface()) {
      return Kind::$INTERFACE;
    } else if ($this->reflect->isSubclassOf(Enum::class)) {
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
    return $this->reflect->isSubclassOf($class);
  }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return  bool
   */
  public function typeImplements($name) {
    return $this->reflect->implementsInterface($name);
  }

  /** @return php.Generator */
  public function allInterfaces() {
    foreach ($this->reflect->getInterfaces() as $interface) {
      yield $interface->name => $this->source->reflect($interface);
    }
  }

  /** @return php.Generator */
  public function declaredInterfaces() {
    $parent= $this->reflect->getParentClass();
    $inherited= $parent ? array_flip($parent->getInterfaceNames()) : [];
    foreach ($this->reflect->getInterfaces() as $interface) {
      if (isset($inherited[$interface->getName()])) continue;
      yield $interface->name => $this->source->reflect($interface);
    }
  }

  /** @return php.Generator */
  public function allTraits() {
    $reflect= $this->reflect;
    do {
      foreach ($reflect->getTraits() as $trait) {
        yield $trait->name => $this->source->reflect($trait);
      }
    } while ($reflect= $reflect->getParentClass());
  }

  /** @return php.Generator */
  public function declaredTraits() {
    foreach ($this->reflect->getTraits() as $trait) {
      yield $trait->name => $this->source->reflect($trait);
    }
  }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name) {
    $reflect= $this->reflect;
    do {
      if (in_array($name, $reflect->getTraitNames(), true)) return true;
    } while ($reflect= $reflect->getParentClass());
    return false;
  }

  /**
   * Maps annotations
   *
   * @param  var $reflect
   * @param  string $member
   * @param  string $kind Either "method" or "field"
   * @return [:var]
   */
  protected function memberAnnotations($reflect, $member, $kind) {
    return $this->codeUnit()->declaration()[$kind][$member]['annotations'];
  }

  /** @return [:var] */
  public function constructor() {
    $ctor= $this->reflect->getConstructor();
    if (null === $ctor) {
      return [
        'name'        => '__default',
        'access'      => Modifiers::IS_PUBLIC,
        'holder'      => $this->reflect->name,
        'comment'     => function() { return null; },
        'params'      => function() { return []; },
        'annotations' => function() { return []; },
      ];
    } else {
      return $this->method($ctor);
    }
  }

  /**
   * Creates a new instance
   *
   * @param  var[] $args
   * @return lang.Generic
   */
  public function newInstance($args) {
    if (!$this->reflect->isInstantiable()) {
      throw new IllegalArgumentException('Verifying '.$this->name.': Cannot instantiate');
    }

    try {
      return $this->reflect->newInstanceArgs($args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Creating a new instance of '.$this->name.' raised '.$e->getClassName(), $e);
    } catch (\Exception $e) {
      throw new IllegalArgumentException('Instantiating '.$this->name.': '.$e->getMessage());
    }
  }

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name) { return $this->reflect->hasProperty($name); }

  /**
   * Maps a field
   *
   * @param  php.ReflectionProperty $reflect
   * @return [:var]
   */
  private function field($reflect) {
    return [
      'name'        => $reflect->name,
      'access'      => new Modifiers($reflect->getModifiers() & ~0x1fb7f008),
      'holder'      => $reflect->getDeclaringClass()->name,
      'annotations' => function() use($reflect) { return $this->memberAnnotations($reflect, $reflect->name, 'field'); },
      'comment'     => function() use($reflect) { return $reflect->getDocComment(); },
      'read'        => function($instance) use($reflect) { return $this->readField($reflect, $instance); },
      'modify'      => function($instance, $value) use($reflect) { $this->modifyField($reflect, $instance, $value); }
    ];
  }

  /**
   * Reads a field
   *
   * @param  php.ReflectionProperty $reflect
   * @param  lang.Generic $instance
   * @return var
   */
  private function readField($reflect, $instance) {
    $reflect->setAccessible(true);
    if ($reflect->isStatic()) {
      return $reflect->getValue(null);
    } else if ($instance && $reflect->getDeclaringClass()->isInstance($instance)) {
      return $reflect->getValue($instance);
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $reflect->name
    ));
  }

  /**
   * Modifies a field
   *
   * @param  php.ReflectionProperty $reflect
   * @param  lang.Generic $instance
   * @param  var $value
   * @return voud
   */
  private function modifyField($reflect, $instance, $value) {
    $reflect->setAccessible(true);
    if ($reflect->isStatic()) {
      $reflect->setValue(null, $value);
      return;
    } else if ($instance && $reflect->getDeclaringClass()->isInstance($instance)) {
      $reflect->setValue($instance, $value);
      return;
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $reflect->name
    ));
  }

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name) {
    try {
      return $this->field($this->reflect->getProperty($name));
    } catch (\Exception $e) {
      throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
    }
  }

  /** @return php.Generator */
  public function allFields() {
    foreach ($this->reflect->getProperties() as $field) {
      yield $field->name => $this->field($field);
    }
  }

  /** @return php.Generator */
  public function declaredFields() {
    foreach ($this->reflect->getProperties() as $field) {
      if ($field->getDeclaringClass()->name !== $this->reflect->name) continue;
      yield $field->name => $this->field($field);
    }
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  private function param($pos, $reflect) {
    if ($reflect->isArray()) {
      $type= function() { return Type::$ARRAY; };
    } else if ($reflect->isCallable()) {
      $type= function() { return Type::$CALLABLE; };
    } else if ($class= $reflect->getClass()) {
      $type= function() use($class) { return new XPClass($class); };
    } else {
      $type= null;
    }

    if ($var= $reflect->isVariadic()) {
      $default= null;
    } else if ($reflect->isOptional()) {
      $default= function() use($reflect) { return $reflect->getDefaultValue(); };
    } else {
      $default= null;
    }

    return [
      'pos'     => $pos,
      'name'    => $reflect->name,
      'type'    => $type,
      'ref'     => $reflect->isPassedByReference(),
      'default' => $default,
      'var'     => $var
    ];
  }

  /**
   * Maps a method
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  private function method($reflect) {
    return [
      'name'        => $reflect->name,
      'access'      => new Modifiers($reflect->getModifiers() & ~0x1fb7f008),
      'holder'      => $reflect->getDeclaringClass()->name,
      'params'      => function() use($reflect) {
        $params= [];
        foreach ($reflect->getParameters() as $pos => $param) {
          $params[]= $this->param($pos, $param);
        }
        return $params;
      },
      'annotations' => function() use($reflect) { return $this->memberAnnotations($reflect, $reflect->name, 'method'); },
      'comment'     => function() use($reflect) { return $reflect->getDocComment(); },
      'invoke'      => function($instance, $args) use($reflect) { return $this->invokeMethod($reflect, $instance, $args); }
    ];
  }

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name) { return $this->reflect->hasMethod($name); }

  /**
   * Invokes the method
   *
   * @param  php.ReflectionMethod $reflect
   * @param  lang.Generic $instance
   * @param  var[] $args
   * @return var
   */
  private function invokeMethod($reflect, $instance, $args) {
    $reflect->setAccessible(true);
    try {
      return $reflect->invokeArgs($instance, $args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Invoking '.$reflect->name.'() raised '.$e->getClassName(), $e);
    } catch (\Exception $e) {
      throw new IllegalArgumentException('Verifying '.$reflect->name.'(): '.$e->getMessage());
    }
  }

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function methodNamed($name) { 
    try {
      return $this->method($this->reflect->getMethod($name));
    } catch (\Exception $e) {
      throw new ElementNotFoundException('No method named '.$name.'() in '.$this->name);
    }
  }

  /** @return php.Generator */
  public function allMethods() {
    foreach ($this->reflect->getMethods() as $method) {
      yield $method->name => $this->method($method);
    }
  }

  /** @return php.Generator */
  public function declaredMethods() {
    foreach ($this->reflect->getMethods() as $method) {
      if ($method->getDeclaringClass()->name !== $this->reflect->name) continue;
      yield $method->name => $this->method($method);
    }
  }

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name) { return $this->reflect->hasConstant($name); }

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function constantNamed($name) {
    if ($this->reflect->hasConstant($name)) {
      return $this->reflect->getConstant($name);
    }
    throw new ElementNotFoundException('No constant named '.$name.'() in '.$this->name);
  }

  /** @return php.Generator */
  public function allConstants() {
    foreach ($this->reflect->getConstants() as $name => $value) {
      yield $name => $value;
    }
  }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    if ('self' === $name || $name === $this->reflect->getShortName()) {
      return $this->source->reflect($this->reflect);
    } else if ('parent' === $name) {
      return $this->reflect->getParentClass();
    } else if ('\\' === $name{0}) {
      return $this->source->reflect(new \ReflectionClass(strtr(substr($name, 1), '.', '\\')));
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return $this->source->reflect(new \ReflectionClass(strtr($name, '.', '\\')));
    } else {
      foreach ($this->codeUnit()->imports() as $imported) {
        if (0 === substr_compare($imported, $name, strrpos($imported, '\\') + 1)) return $this->source->reflect(new \ReflectionClass($imported));
      }
      $ns= $this->reflect->getNamespaceName();
      return $this->source->reflect(new \ReflectionClass(($ns ? $ns.'\\' : '').$name));
    }
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