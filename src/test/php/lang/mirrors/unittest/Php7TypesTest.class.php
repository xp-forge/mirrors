<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\mirrors\unittest\fixture\FixtureBase;
use lang\{Primitive, Type, XPClass};
use unittest\actions\RuntimeVersion;
use unittest\{Action, Test, TestCase};

abstract class Php7TypesTest extends TestCase {
  use TypeDefinition;

  /**
   * Returns a fixture for a given class declaration
   *
   * @param  lang.XPClass $class
   * @return lang.mirrors.TypeMirror
   */
  protected abstract function newFixture($class);

  #[Test]
  public function primitive_return_type() {
    $fixture= $this->define('{
      public function fixture(): int { return 0; }
    }');

    $this->assertEquals(Primitive::$INT, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function array_return_type() {
    $fixture= $this->define('{
      public function fixture(): array { return []; }
    }');

    $this->assertEquals(Type::$ARRAY, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function callable_return_type() {
    $fixture= $this->define('{
      public function fixture(): callable { return []; }
    }');

    $this->assertEquals(Type::$CALLABLE, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function self_return_type() {
    $fixture= $this->define('{
      public function fixture(): self { return new self(); }
    }');

    $this->assertEquals($fixture, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function parent_return_type() {
    $fixture= $this->define(
      '{ public function fixture(): parent { return new parent(); } }',
      [FixtureBase::class]
    );

    $this->assertEquals(new XPClass(FixtureBase::class), $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function value_return_type() {
    $fixture= $this->define('{
      public function fixture(): \lang\Value { /* TBI */ }
    }');

    $this->assertEquals(XPClass::forName('lang.Value'), $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test, Action(eval: '[new RuntimeVersion(">=7.1.0-dev")]')]
  public function void_return_type() {
    $fixture= $this->define('{
      public function fixture(): void { }
    }');

    $this->assertEquals(Type::$VOID, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test, Action(eval: 'new RuntimeVersion(">=7.1.0-dev")')]
  public function nullable_return_type() {
    $fixture= $this->define('{
      public function fixture(): ?string { return null; }
    }');

    $this->assertEquals(Primitive::$STRING, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test, Action(eval: '[new RuntimeVersion(">=7.1.0-dev")]')]
  public function iterable_return_type() {
    $fixture= $this->define('{
      public function fixture(): iterable { return null; }
    }');

    $this->assertEquals(Type::$ITERABLE, $this->newFixture($fixture)->methods()->named('fixture')->returns());
  }

  #[Test]
  public function primitive_parameter_type() {
    $fixture= $this->define('{
      public function fixture(int $param) { }
    }');

    $this->assertEquals(Primitive::$INT, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }

  #[Test, Action(eval: 'new RuntimeVersion(">=7.1.0-dev")')]
  public function nullable_parameter_type() {
    $fixture= $this->define('{
      public function fixture(?int $param) { }
    }');

    $this->assertEquals(Primitive::$INT, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }

  #[Test]
  public function self_parameter_type() {
    $fixture= $this->define('{
      public function fixture(self $param) { }
    }');

    $this->assertEquals($fixture, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }

  #[Test, Action(eval: '[new RuntimeVersion(">=7.1.0-dev")]')]
  public function iterable_parameter_type() {
    $fixture= $this->define('{
      public function fixture(iterable $param) { }
    }');

    $this->assertEquals(Type::$ITERABLE, $this->newFixture($fixture)->methods()->named('fixture')->parameters()->first()->type());
  }
}