<?php namespace lang\mirrors\unittest;

use lang\Primitive;
use lang\Type;
use lang\XPClass;
use lang\ClassLoader;
use lang\mirrors\TypeMirror;
use unittest\actions\RuntimeVersion;

#[@action(new RuntimeVersion('>=7.0.0-dev'))]
class Php7TypesTest extends \unittest\TestCase {

  #[@test]
  public function primitive_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): int { return 0; }
    }');

    $this->assertEquals(Primitive::$INT, (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function array_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): array { return []; }
    }');

    $this->assertEquals(Type::$ARRAY, (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function callable_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): callable { return []; }
    }');

    $this->assertEquals(Type::$CALLABLE, (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function self_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): self { return new self(); }
    }');

    $this->assertEquals($fixture, (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function parent_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): parent { return new parent(); }
    }');

    $this->assertEquals(XPClass::forName('lang.Object'), (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function object_return_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(): \lang\Object { return new \lang\Object(); }
    }');

    $this->assertEquals(XPClass::forName('lang.Object'), (new TypeMirror($fixture))->methods()->named('fixture')->returns());
  }

  #[@test]
  public function primitive_parameter_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(int $param) { }
    }');

    $this->assertEquals(Primitive::$INT, (new TypeMirror($fixture))->methods()->named('fixture')->parameters()->first()->type());
  }

  #[@test]
  public function self_parameter_type() {
    $fixture= ClassLoader::defineClass($this->name, 'lang.Object', [], '{
      public function fixture(self $param) { }
    }');

    $this->assertEquals($fixture, (new TypeMirror($fixture))->methods()->named('fixture')->parameters()->first()->type());
  }
}