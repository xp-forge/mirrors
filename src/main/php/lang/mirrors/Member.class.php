<?php namespace lang\mirrors;

/**
 * Base class for all type members: Fields, methods, constructors.
 */
abstract class Member extends \lang\Object {
  protected static $member;
  protected $mirror, $reflect;

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
    return new Annotations($this->mirror, isset($lookup[$name]) ? (array)$lookup[$name]['annotations'] : []);
  }
}