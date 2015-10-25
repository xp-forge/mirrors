<?php namespace lang\mirrors\unittest;

use lang\Primitive;
use lang\Type;
use lang\Object;
use lang\XPClass;
use lang\ClassLoader;
use lang\mirrors\TypeMirror;

abstract class Php7TypesTest extends \unittest\TestCase {
  private static $uniq= 0;

  /**
   * Defines a type
   *
   * @param  string $body
   * @return lang.XPClass
   */
  protected function define($body) {
    $declaration= [
      'kind'       => 'class',
      'extends'    => [Object::class],
      'implements' => [],
      'use'        => []
    ];
    return ClassLoader::defineType(nameof($this).self::$uniq++, $declaration, $body);
  }

  /**
   * Returns a fixture for a given class declaration
   *
   * @param  lang.XPClass $class
   * @return lang.mirrors.TypeMirror
   */
  protected abstract function newFixture($class);

  #[@test]
  public function primitive_return_type() {
    $fixture= $this->define('{
      public function fixture(): int { return 0; }
    }');

    $this->assertEquals(Primitive::$INT, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function array_return_type() {
    $fixture= $this->define('{
      public function fixture(): array { return []; }
    }');

    $this->assertEquals(Type::$ARRAY, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function callable_return_type() {
    $fixture= $this->define('{
      public function fixture(): callable { return []; }
    }');

    $this->assertEquals(Type::$CALLABLE, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function self_return_type() {
    $fixture= $this->define('{
      public function fixture(): self { return new self(); }
    }');

    $this->assertEquals($fixture, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function parent_return_type() {
    $fixture= $this->define('{
      public function fixture(): parent { return new parent(); }
    }');

    $this->assertEquals(XPClass::forName('lang.Object'), $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function object_return_type() {
    $fixture= $this->define('{
      public function fixture(): \lang\Object { return new \lang\Object(); }
    }');

    $this->assertEquals(XPClass::forName('lang.Object'), $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[@test]
  public function primitive_parameter_type() {
    $fixture= $this->define('{
      public function fixture(int $param) { }
    }');

    $this->assertEquals(Primitive::$INT, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }

  #[@test]
  public function self_parameter_type() {
    $fixture= $this->define('{
      public function fixture(self $param) { }
    }');

    $this->assertEquals($fixture, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }
}