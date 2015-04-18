<?php namespace lang\mirrors;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\ClassSource;
use lang\ClassNotFoundException;
use lang\IllegalArgumentException;
use lang\XPClass;

/**
 * Reference type mirrors
 *
 * @test   xp://lang.mirrors.unittest.TypeMirrorConstantsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorFieldsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorMethodsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorTest
 */
class TypeMirror extends \lang\Object {
  private $methods, $fields;
  public $reflect;

  /**
   * Creates a new mirrors instance
   *
   * @param  var $arg Either a php.ReflectionClass, an XPClass instance or a string with the FQCN
   * @param  lang.mirrors.Sources $source
   * @throws lang.ClassNotFoundException
   */
  public function __construct($arg, Sources $source= null) {
    if ($arg instanceof \ReflectionClass) {
      $this->reflect= new FromReflection($arg);
    } else if ($arg instanceof XPClass) {
      $this->reflect= new FromReflection($arg->_reflect);
    } else if ($arg instanceof Source) {
      $this->reflect= $arg;
    } else if (null === $source) {
      $this->reflect= Sources::$REFLECTION->reflect($arg);
    } else {
      $this->reflect= $source->reflect($arg);
    }

    $this->methods= new Methods($this);
    $this->fields= new Fields($this);
  }

  /** @return string */
  public function name() { return $this->reflect->typeName(); }

  /** @return string */
  public function declaration() { return $this->reflect->typeDeclaration(); }

  /** @return string */
  public function comment() {
    $comment= $this->reflect->typeComment();
    return false === $comment ? null : trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
      $comment,
      4,                              // "/**\n"
      strpos($comment, '* @')- 2      // position of first details token
    )));
  }

  /** @return lang.mirrors.Package */
  public function package() { return new Package($this->reflect->packageName()); }

  /** @return self */
  public function parent() {
    $parent= $this->reflect->typeParent();
    return $parent ? new self($parent) : null;
  }

  /** @return lang.mirrors.Traits */
  public function traits() { return new Traits($this); }

  /** @return lang.mirrors.Interfaces */
  public function interfaces() { return new Interfaces($this); }

  /** @return lang.mirrors.parse.CodeUnit */
  public function unit() { return $this->reflect->codeUnit(); }

  /** @return lang.mirrors.Kind */
  public function kind() { return $this->reflect->typeKind(); }

  /** @return lang.mirrors.Constructor */
  public function constructor() { return new Constructor($this); }

  /** @return lang.mirrors.Methods */
  public function methods() { return $this->methods; }

  /** @return lang.mirrors.Fields */
  public function fields() { return $this->fields; }

  /** @return lang.mirrors.Constants */
  public function constants() { return new Constants($this); }

  /** @return lang.mirrors.Modifiers */
  public function modifiers() { return $this->reflect->typeModifiers(); }

  /** @return lang.mirrors.Annotations */
  public function annotations() {
    $lookup= $this->reflect->typeAnnotations();
    return new Annotations($this, isset($lookup[null]) ? $lookup[null] : []);
  }

  /**
   * Resolves a type name in the context of this mirror
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    return new self($this->reflect->resolve($name));
  }

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  var $arg Either a TypeMirror or a string
   * @return bool
   */
  public function isSubtypeOf($arg) {
    $type= $arg instanceof self ? $arg->reflect->name : strtr($arg, '.', '\\');
    return $this->reflect->isSubtypeOf($type);
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