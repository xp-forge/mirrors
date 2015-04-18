<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\Enum;
use lang\IllegalArgumentException;
use lang\Throwable;

class FromReflection extends \lang\Object implements Source {
  private $reflect;
  private $unit= null;
  public $name;
  private static $DEFAULT;

  static function __static() {
    self::$DEFAULT= new \ReflectionMethod(self::class, '__default');
  }

  /** @return lang.Generic */
  public function __default() {
    return $this->reflect->newInstance();
  }

  public function __construct(\ReflectionClass $reflect) {
    $this->reflect= $reflect;
    $this->name= $reflect->name;
  }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() {
    if (null === $this->unit) {
      $this->unit= (new ClassSyntax())->parse(new ClassSource($this->typeName()));
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
    return $parent ? new self($parent) : null;
  }

  /** @return string */
  public function typeComment() {
    $comment= $this->reflect->getDocComment();
    return false === $comment ? null : $comment;
  }

  /** @return var */
  public function typeAnnotations() {
    return $this->codeUnit()->declaration()['annotations'];
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

  /** @return [:var] */
  public function constructor() {
    $ctor= $this->reflect->getConstructor();
    if (null === $ctor) {
      return [
        'name'    => '__default',
        'access'  => Modifiers::IS_PUBLIC,
        'holder'  => $this->reflect->name,
        'comment' => function() { return null; },
        'params'  => function() { return []; },
        'value'   => self::$DEFAULT
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

  /** @return bool */
  public function hasField($name) { return $this->reflect->hasProperty($name); }

  /**
   * Maps a field
   *
   * @param  php.ReflectionProperty $reflect
   * @return [:var]
   */
  private function field($reflect) {
    $reflect->setAccessible(true);
    return [
      'name'    => $reflect->name,
      'access'  => $reflect->getModifiers() & ~0x1fb7f008,
      'holder'  => $reflect->getDeclaringClass()->name,
      'comment' => function() use($reflect) { return $reflect->getDocComment(); },
      'value'   => $reflect
    ];
  }

  /**
   * Reads a field
   */
  public function readField($reflect, $instance) {
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
   */
  public function modifyField($reflect, $instance, $value) {
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

  /** @return [:var] */
  public function fieldNamed($name) { return $this->field($this->reflect->getProperty($name)); }

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

  /** @return bool */
  public function hasMethod($name) { return $this->reflect->hasMethod($name); }

  /**
   * Invokes the method
   */
  public function invokeMethod($reflect, $instance, $args) {
    try {
      return $reflect->invokeArgs($instance, $args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Invoking '.$reflect->name.'() raised '.$e->getClassName(), $e);
    } catch (\Exception $e) {
      throw new IllegalArgumentException('Verifying '.$reflect->name.'(): '.$e->getMessage());
    }
  }

  /** @return [:var] */
  public function methodNamed($name) { return $this->method($this->reflect->getMethod($name)); }

  /**
   * Maps a method
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  private function method($reflect) {
    $reflect->setAccessible(true);
    return [
      'name'    => $reflect->name,
      'access'  => $reflect->getModifiers() & ~0x1fb7f008,
      'holder'  => $reflect->getDeclaringClass()->name,
      'params'  => function() use($reflect) { return $reflect->getParameters(); },
      'comment' => function() use($reflect) { return $reflect->getDocComment(); },
      'value'   => $reflect
    ];
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

  /** @return bool */
  public function hasConstant($name) { return $this->reflect->hasConstant($name); }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    if ('self' === $name || $name === $this->reflect->getShortName()) {
      return new self($this->reflect);
    } else if ('parent' === $name) {
      return $this->reflect->getParentClass();
    } else if ('\\' === $name{0}) {
      return new self(new \ReflectionClass(strtr(substr($name, 1), '.', '\\')));
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return new self(new \ReflectionClass(strtr($name, '.', '\\')));
    } else {
      foreach ($this->codeUnit()->imports() as $imported) {
        if (0 === substr_compare($imported, $name, strrpos($imported, '.') + 1)) return new self(new \ReflectionClass(strtr($imported, '.', '\\')));
      }
      return new self(new \ReflectionClass($this->reflect->getNamespaceName().'\\'.$name));
    }
  }

  public function __call($name, $args) {
    return $this->reflect->{$name}(...$args);
  }

  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}