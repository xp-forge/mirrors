<?php namespace lang\mirrors;

use lang\mirrors\parse\TagsSyntax;
use lang\mirrors\parse\TagsSource;

/**
 * Base class for all type members: Fields, methods, constructors.
 */
abstract class Member extends \lang\Object {
  public static $STATIC= 0x0001, $INSTANCE= 0x0002, $DECLARED= 0x0004;
  public $reflect;
  protected $mirror;
  private $tags= null;

  /**
   * Creates a new member
   *
   * @param  lang.mirrors.TypeMirror $mirror The type this member belongs to.
   * @param  [:var] $reflect
   */
  public function __construct($mirror, $reflect) {
    $this->mirror= $mirror;
    $this->reflect= $reflect;
  }

  /** @return string */
  public function name() { return $this->reflect['name']; }

  /** @return lang.mirrors.Modifiers */
  public function modifiers() { return $this->reflect['access']; }

  /** @return string */
  public function comment() {
    $raw= $this->reflect['comment']();
    if ($raw) {
      $text= trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
        $raw,
        4,                          // "/**\n"
        strpos($raw, '* @') - 2     // position of first details token
      )));
      return '' === $text ? null : $text;
    }
    return null;
  }

  /**
   * Returns tags from doc comment
   *
   * @return [:var]
   */
  public function tags() {
    if (null === $this->tags) {
      $raw= $this->reflect['comment']();
      if ($raw) {
        $parsed= (new TagsSyntax())->parse(new TagsSource(preg_replace('/\n\s+\* ?/', "\n", substr(
          $raw,
          strpos($raw, '* @') + 2,    // position of first details token
          - 2                         // "*/"
        ))));
        $this->tags= array_merge(static::$tags, $parsed);
      } else {
        $this->tags= static::$tags;
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
    return $this->mirror->resolve($this->reflect['holder']);
  }

  /** @return lang.mirrors.Annotations */
  public function annotations() {
    $annotations= $this->reflect['annotations']();
    return new Annotations($this->mirror, isset($annotations[null]) ? $annotations[null] : []);
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
      $this->reflect['holder'] === $cmp->reflect['holder']
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