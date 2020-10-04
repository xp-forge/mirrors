<?php namespace lang\mirrors\unittest;

use lang\ClassLoader;
use lang\mirrors\unittest\fixture\Identity;
use lang\mirrors\{Sources, TypeMirror};

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
  protected function define($declaration, $extends= null) {
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
  protected function mirror($body, $extends= null) {
    return new TypeMirror($this->define($body, $extends), $this->source());
  }
}