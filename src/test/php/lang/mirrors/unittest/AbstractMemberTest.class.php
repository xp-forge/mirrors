<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\Identity;
use lang\ClassLoader;
use lang\Object;

abstract class AbstractMemberTest extends \unittest\TestCase {
  private static $uniq= 0;
  protected $type;

  /** @return void */
  public function setUp() {
    $this->type= new TypeMirror(static::class);
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