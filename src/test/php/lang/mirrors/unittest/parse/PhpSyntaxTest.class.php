<?php namespace lang\mirrors\unittest\parse;

use lang\Primitive;
use lang\mirrors\parse\{Closure, CodeUnit, NewInstance, PhpSyntax, TypeRef, Value};
use unittest\{Action, Test};

class PhpSyntaxTest extends \unittest\TestCase {

  /**
   * Parses a string
   *
   * @param  string $input
   * @return lang.mirrors.parse.CodeUnit
   */
  private function parse($input) {
    return (new PhpSyntax())->parse(new StringInput($input));
  }

  #[Test]
  public function object_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Object', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php class Object { }')
    );
  }

  #[Test]
  public function interface_without_parent() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A { }')
    );
  }

  #[Test]
  public function interface_with_parent() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => ['B'], 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A extends B { }')
    );
  }

  #[Test]
  public function interface_with_multiple_parents() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => ['B', 'C'], 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A extends B, C { }')
    );
  }

  #[Test]
  public function runnable_impl() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => ['Runnable'], 'name' => 'Test', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php class Test implements Runnable { }')
    );
  }

  #[Test]
  public function creation_trait() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'trait', 'comment' => null, 'parent' => null, 'name' => 'Creation', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php trait Creation { }')
    );
  }

  #[Test]
  public function creation_user() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null, 'use' => [
        'Creation' => ['kind' => 'use', 'name' => 'Creation']
      ]]),
      $this->parse('<?php class Test { use Creation; }')
    );
  }

  #[Test]
  public function class_using_trait_with_alias() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null, 'use' => [
        'Creation' => ['kind' => 'use', 'name' => 'Creation']
      ]]),
      $this->parse('<?php class Test { use Creation { value as name; } }')
    );
  }

  #[Test]
  public function class_using_trait_with_aliases() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null, 'use' => [
        'Creation' => ['kind' => 'use', 'name' => 'Creation']
      ]]),
      $this->parse('<?php class Test { use Creation {
        a as b;
        c as d;
      } }')
    );
  }

  #[Test]
  public function abstract_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => ['abstract'], 'annotations' => null]),
      $this->parse('<?php abstract class Test { }')
    );
  }

  #[Test]
  public function final_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => ['final'], 'annotations' => null]),
      $this->parse('<?php final class Test { }')
    );
  }

  #[Test]
  public function documented_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => '/** Doc */', 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php /** Doc */ class Test { }')
    );
  }

  #[Test]
  public function no_imports() {
    $this->assertEquals(
      [],
      $this->parse('<?php class Test { }')->imports()
    );
  }

  #[Test]
  public function one_import() {
    $this->assertEquals(
      ['Objects' => 'util\Objects'],
      $this->parse('<?php use util\Objects; class Test { }')->imports()
    );
  }

  #[Test]
  public function imports() {
    $this->assertEquals(
      ['Objects' => 'util\Objects', 'Date' => 'util\Date'],
      $this->parse('<?php use util\Objects; use util\Date; class Test { }')->imports()
    );
  }

  #[Test]
  public function aliased_import() {
    $this->assertEquals(
      ['Aliased' => 'util\Objects'],
      $this->parse('<?php use util\Objects as Aliased; class Test { }')->imports()
    );
  }

  #[Test]
  public function useless_but_syntactically_valid_single_grouped_import() {
    $this->assertEquals(
      ['Date' => 'util\Date'],
      $this->parse('<?php use util\{Date}; class Test { }')->imports()
    );
  }

  #[Test]
  public function grouped_imports() {
    $this->assertEquals(
      ['Date' => 'util\Date', 'DateUtil' => 'util\DateUtil'],
      $this->parse('<?php use util\{Date, DateUtil}; class Test { }')->imports()
    );
  }

  #[Test]
  public function new_import() {
    $this->assertEquals(
      ['ArrayListExtensions' => 'util\ArrayListExtensions'],
      $this->parse('<?php new import("util.ArrayListExtensions"); class Test { }')->imports()
    );
  }

  #[Test]
  public function test_class() {
    $this->assertEquals(
      new CodeUnit(
        'de\thekid\test',
        ['Objects' => 'util\Objects'],
        [
          'kind'        => 'class',
          'name'        => 'IntegrationTest',
          'parent'      => '\unittest\TestCase',
          'implements'  => null,
          'modifiers'   => [],
          'comment'     => null, 
          'annotations' => null,
          'field' => [
            'fixture' => [
              'kind'        => 'field',
              'name'        => 'fixture',
              'init'        => null,
              'access'      => ['private'],
              'annotations' => null,
              'comment'     => null
            ]
          ],
          'method' => [
            'connect' => [
              'kind'        => 'method',
              'name'        => 'connect',
              'params'      => [[
                'name'        => 'arg',
                'type'        => null,
                'ref'         => false,
                'var'         => false,
                'default'     => null,
                'this'        => [],
                'annotations' => null
              ]],
              'access'      => ['private'],
              'annotations' => [null => [], '$arg' => ['inject' => new Value('db')]],
              'comment'     => null,
              'returns'     => null
            ],
            'can_create' => [
              'kind'        => 'method',
              'name'        => 'can_create',
              'params'      => [],
              'access'      => ['public'],
              'annotations' => [null => ['test' => null]],
              'comment'     => null,
              'returns'     => null
            ]
          ]
        ]
      ),
      $this->parse('<?php namespace de\thekid\test;

        use util\Objects;

        class IntegrationTest extends \unittest\TestCase {
          private $fixture;

          #[@$arg: inject("db")]
          private function connect($arg) { /* ... */ }

          #[@test]
          public function can_create() { /* ... */ }
        }
      ')
    );
  }

  #[Test]
  public function compact_field_syntax() {
    $this->assertEquals(
      [
        'a' => [
          'kind'        => 'field',
          'name'        => 'a',
          'init'        => null,
          'access'      => ['private'],
          'annotations' => null,
          'comment'     => null
        ],
        'b' => [
          'kind'        => 'field',
          'name'        => 'b',
          'init'        => null,
          'access'      => [],
          'annotations' => null,
          'comment'     => null
        ]
      ],
      $this->parse('<?php class Test { private $a, $b; }')->declaration()['field']
    );
  }

  #[Test]
  public function method_return_type() {
    $this->assertEquals(
      [
        'a' => [
          'kind'        => 'method',
          'name'        => 'a',
          'params'      => [],
          'access'      => [],
          'annotations' => null,
          'comment'     => null,
          'returns'     => new TypeRef(Primitive::$INT)
        ]
      ],
      $this->parse('<?php class Test { function a(): int; }')->declaration()['method']
    );
  }

  #[Test]
  public function multi_line_annotation() {
    $parsed= $this->parse('<?php 
      #[Action(new \unittest\actions\VerifyThat(function() { throw new \lang\IllegalStateException("Test");}))]
      class Test { }
    ');
    $this->assertEquals(
      new NewInstance(
        '\unittest\actions\VerifyThat',
        [new Closure([], 'throw new \lang\IllegalStateException("Test");')]
      ),
      $parsed->declaration()['annotations'][null]['action']
    );
  }
}