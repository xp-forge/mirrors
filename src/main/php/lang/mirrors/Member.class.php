<?php namespace lang\mirrors;

use lang\mirrors\parse\TagsSyntax;
use lang\mirrors\parse\TagsSource;

/**
 * Base class for all type members: Fields, methods, constructors.
 */
abstract class Member extends \lang\Object {
  public static $STATIC= 0, $INSTANCE= 1;
  public $reflect;
  protected $mirror;
  private $tags= null;

  /**
   * Creates a new method
   *
   * @param  lang.mirrors.TypeMirror $mirror The type this member belongs to.
   * @param  var $reflect A reflection object
   */
  public function __construct($mirror, $reflect) {
    $this->mirror= $mirror;
    $this->reflect= $reflect;
  }

  /** @return string */
  public function name() { return $this->reflect->name; }

  /** @return lang.mirrors.Modifiers */
  public function modifiers() { return new Modifiers($this->reflect->getModifiers() & ~0x1fb7f008); }

  /** @return string */
  public function comment() {
    $raw= $this->reflect->getDocComment();
    if (false === $raw) {
      return null;
    } else {
      $text= trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
        $raw,
        4,                          // "/**\n"
        strpos($raw, '* @') - 2     // position of first details token
      )));
      return '' === $text ? null : $text;
    }
  }

  /**
   * Returns tags from doc comment
   *
   * @return [:var]
   */
  public function tags() {
    if (null === $this->tags) {
      if ($raw= $this->reflect->getDocComment()) {
        $parsed= (new TagsSyntax())->parse(new TagsSource(preg_replace('/\n\s+\* ?/', "\n", substr(
          $raw,
          strpos($raw, '* @') + 2,    // position of first details token
          - 2                         // "*/"
        ))));
        $this->tags= array_merge(static::$tags, $parsed);
      }
    }
    return $this->tags;
  }

  /**
   * Returns the type this member was declared in. Via inheritance, this may
   * differ from the type this member was created with.
   *
   * @return lang.mirrors.TypeMirror
   */
  public function declaredIn() { 
    $declaring= $this->reflect->getDeclaringClass();
    if ($declaring->name === $this->mirror->reflect->name) {
      return $this->mirror;
    } else {
      return new TypeMirror($declaring);
    }
  }

  /** @return lang.mirrors.Annotations */
  public function annotations() {
    $lookup= $this->mirror->unit()->declaration()[static::$kind];
    $name= $this->reflect->name;
    return new Annotations(
      $this->mirror,
      isset($lookup[$name]['annotations'][null]) ? (array)$lookup[$name]['annotations'][null] : []
    );
  }

  /**
   * Returns whether a given value is equal to this member
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->name === $cmp->name &&
      $this->reflect->getDeclaringClass()->name === $cmp->reflect->getDeclaringClass()->name
    );
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this.')';
  }

  /** @return string */
  public abstract function __toString();
}