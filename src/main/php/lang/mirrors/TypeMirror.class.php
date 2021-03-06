<?php namespace lang\mirrors;

use lang\IllegalArgumentException;
use lang\mirrors\parse\{ClassSource, ClassSyntax};

/**
 * Reference type mirrors
 *
 * @test   xp://lang.mirrors.unittest.TypeMirrorAnnotationsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorConstantsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorFieldsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorInterfacesTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorMethodsTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorResolveTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorTest
 * @test   xp://lang.mirrors.unittest.TypeMirrorTraitsTest
 */
class TypeMirror implements \lang\Value {
  private $methods= null;
  private $fields= null;
  private $annotations= null;
  public $reflect;

  /**
   * Creates a new mirrors instance
   *
   * @param  lang.XPClass|lang.mirrors.Source|php.ReflectionClass|string $arg
   * @param  lang.mirrors.Sources $source
   */
  public function __construct($arg, Sources $source= null) {
    if ($arg instanceof Source) {
      $this->reflect= $arg;
    } else if (null === $source) {
      $this->reflect= Sources::$DEFAULT->reflect($arg);
    } else {
      $this->reflect= $source->reflect($arg);
    }
  }

  /** @return bool */
  public function present() { return $this->reflect->present(); }

  /** @return string */
  public function name() { return $this->reflect->typeName(); }

  /** @return string */
  public function declaration() { return $this->reflect->typeDeclaration(); }

  /** @return lang.Type */
  public function type() { return $this->reflect->typeInstance(); }

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

  /** @return lang.mirrors.Modifiers */
  public function modifiers() { return $this->reflect->typeModifiers(); }

  /** @return lang.mirrors.Constructor */
  public function constructor() { return new Constructor($this); }

  /** @return lang.mirrors.Constants */
  public function constants() { return new Constants($this); }

  /** @return lang.mirrors.Methods */
  public function methods() { return $this->methods ?: $this->methods= new Methods($this); }

  /**
   * Returns a method by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Method
   * @throws lang.ElementNotFoundException
   */
  public function method($named) { return $this->methods()->named($named); }

  /** @return lang.mirrors.Fields */
  public function fields() { return $this->fields ?: $this->fields= new Fields($this); }

  /**
   * Returns a field by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Field
   * @throws lang.ElementNotFoundException
   */
  public function field($named) { return $this->fields()->named($named); }

  /** @return lang.mirrors.Annotations */
  public function annotations() { return $this->annotations ?: $this->annotations= new Annotations($this, (array)$this->reflect->typeAnnotations()); }

  /**
   * Returns a annotation by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Annotation
   * @throws lang.ElementNotFoundException
   */
  public function annotation($named) { return $this->annotations()->named($named); }

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
   * @param  string|self $arg
   * @return bool
   */
  public function isSubtypeOf($arg) {
    $type= $arg instanceof self ? $arg->reflect->name : strtr($arg, '.', '\\');
    return $this->reflect->isSubtypeOf($type);
  }

  /**
   * Compares a given value to this type mirror
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->reflect->compareTo($value->reflect) : 1;
  }

  /** @return string */
  public function hashCode() { return 'M'.md5($this->name()); }

  /** @return string */
  public function toString() { return nameof($this).'<'.$this->name().'>'; }
}