<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\ClassNotFoundException;
use lang\IllegalArgumentException;
use lang\XPClass;
use lang\Enum;

/**
 * Reference type mirrors
 *
 * @test   xp://lang.mirrors.unittest.TypeMirrorConstantsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorFieldsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorMethodsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorTest
 */
class TypeMirror extends \lang\Object {
  private $methods, $fields, $constants;
  private $kind, $unit;
  public $reflect;

  /**
   * Creates a new mirrors instance
   *
   * @param  var $arg Either a php.ReflectionClass or a string with the FQCN
   * @throws lang.ClassNotFoundException
   */
  public function __construct($arg) {
    if ($arg instanceof \ReflectionClass) {
      $this->reflect= $arg;
    } else if ($arg instanceof XPClass) {
      $this->reflect= $arg->_reflect;
    } else {
      try {
        $this->reflect= new \ReflectionClass(strtr($arg, '.', '\\'));
      } catch (\Exception $e) {
        throw new ClassNotFoundException($arg);
      }
    }

    $this->methods= new Methods($this);
    $this->fields= new Fields($this);
    $this->constants= new Constants($this);
  }

  /** @return string */
  public function name() { return strtr($this->reflect->getName(), '\\', '.'); }

  /** @return string */
  public function declaration() { return $this->reflect->getShortName(); }

  /** @return string */
  public function comment() {
    $comment= $this->reflect->getDocComment();
    return false === $comment ? null : trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
      $comment,
      4,                              // "/**\n"
      strpos($comment, '* @')- 2      // position of first details token
    )));
  }

  /** @return lang.mirrors.Package */
  public function package() { return new Package($this->reflect->getNamespaceName()); }

  /** @return self */
  public function parent() {
    $parent= $this->reflect->getParentClass();
    return $parent ? new self($parent) : null;
  }

  /** @return lang.mirrors.Traits */
  public function traits() { return new Traits($this); }

  /** @return lang.mirrors.Interfaces */
  public function interfaces() { return new Interfaces($this); }

  /** @return lang.mirrors.parse.CodeUnit */
  public function unit() {
    if (null === $this->unit) {
      $this->unit= (new ClassSyntax())->parse(new ClassSource($this->name()));
    }
    return $this->unit;
  }

  /** @return lang.mirrors.Kind */
  public function kind() {
    if (null === $this->kind) {
      if ($this->reflect->isTrait()) {
        $this->kind= Kind::$TRAIT;
      } else if ($this->reflect->isInterface()) {
        $this->kind= Kind::$INTERFACE;
      } else if ($this->reflect->isSubclassOf(Enum::class)) {
        $this->kind= Kind::$ENUM;
      } else {
        $this->kind= Kind::$CLASS;
      }
    }
    return $this->kind;
  }

  /** @return lang.mirrors.Constructor */
  public function constructor() {
    return new Constructor($this);
  }

  /** @return lang.mirrors.Methods */
  public function methods() { return $this->methods; }

  /** @return lang.mirrors.Fields */
  public function fields() { return $this->fields; }

  /** @return lang.mirrors.Constants */
  public function constants() { return $this->constants; }

  /** @return lang.mirrors.Annotations */
  public function annotations() {
    $lookup= $this->unit()->declaration()['annotations'];
    return new Annotations($this, isset($lookup[null]) ? $lookup[null] : []);
  }

  /**
   * Resolves a type name in the context of this mirror
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    if ('self' === $name) {
      return $this;
    } else if ('parent' === $name) {
      return $this->parent();
    } else if (strstr($name, '\\') || strstr($name, '.')) {
      return new self($name);
    } else if ($name === $this->reflect->getShortName()) {
      return $this;
    } else {
      $unit= $this->unit();
      foreach ($unit->imports() as $imported) {
        if (0 === substr_compare($imported, $name, strrpos($imported, '.') + 1)) return new self($imported);
      }
      return new self($unit->package().'.'.$name);
    }
  }

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  var $arg Either a TypeMirror or a string
   * @return bool
   */
  public function isSubtypeOf($arg) {
    $type= $arg instanceof self ? $arg->reflect : strtr($arg, '.', '\\');
    return $this->reflect->isSubclassOf($type);
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->reflect->name === $cmp->reflect->name;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->name().'>';
  }
}