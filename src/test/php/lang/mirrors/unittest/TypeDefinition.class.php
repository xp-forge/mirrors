<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\Sources;
use lang\mirrors\unittest\fixture\Identity;
use lang\ClassLoader;
use lang\Object;

trait TypeDefinition {
  private static $fixtures= [];

  /** @return lang.mirrors.Source */
  protected function source() { return Sources::$REFLECTION; }

  /**
   * Defines a type
   *
   * @param  string $declaration
   * @param  string[] $extends
   * @return lang.XPClass
   */
  protected function define($declaration, $extends= [Object::class]) {
    if (!isset(self::$fixtures[$declaration])) {
      $definition= [
        'kind'       => 'class',
        'extends'    => $extends,
        'implements' => [],
        'use'        => [],
        'imports'    => [Identity::class => null]
      ];
      self::$fixtures[$declaration]= ClassLoader::defineType(
        nameof($this).sizeof(self::$fixtures),
        $definition,
        $declaration
      );
    }
    return self::$fixtures[$declaration];
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