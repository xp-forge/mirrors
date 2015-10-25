<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Sources;
use lang\mirrors\unittest\fixture\Identity;
use lang\ClassLoader;
use lang\Object;

trait TypeDefinition {
  private static $uniq= 0;

  /** @return lang.mirrors.Source */
  protected function source() { return Sources::$REFLECTION; }

  /**
   * Defines a type
   *
   * @param  string $body
   * @param  string[] $extends
   * @return lang.XPClass
   */
  protected function define($body, $extends= [Object::class]) {
    $declaration= [
      'kind'       => 'class',
      'extends'    => $extends,
      'implements' => [],
      'use'        => [],
      'imports'    => [Identity::class => 'Identity']
    ];
    return ClassLoader::defineType(nameof($this).self::$uniq++, $declaration, $body);
  }

  /**
   * Defines a type
   *
   * @param  string $body
   * @param  string[] $extends
   * @return lang.mirrors.TypeMirror
   */
  protected function mirror($body, $extends= [Object::class]) {
    return new TypeMirror($this->define($body, $extends), $this->source());
  }
}