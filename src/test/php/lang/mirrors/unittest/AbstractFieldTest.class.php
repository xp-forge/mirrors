<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\Identity;
use lang\ClassLoader;
use lang\Object;

abstract class AbstractFieldTest extends \unittest\TestCase {
  private static $uniq= 0;
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(static::class);
  }

  /**
   * Retrieves a fixture method by a given name
   *
   * @param  string $name
   * @return lang.mirrors.Field
   */
  protected function fixture($name) {
    return $this->type->fields()->named($name);
  }

  /**
   * Defines a type
   *
   * @param  string $body
   * @return lang.mirrors.TypeMirror
   */
  protected function define($body) {
    $declaration= [
      'kind'       => 'class',
      'extends'    => [Object::class],
      'implements' => [],
      'use'        => [],
      'imports'    => [Identity::class => 'Identity']
    ];
    return new TypeMirror(ClassLoader::defineType(nameof($this).self::$uniq++, $declaration, $body));
  }
}