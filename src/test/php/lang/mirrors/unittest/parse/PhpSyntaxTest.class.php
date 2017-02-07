<?php namespace lang\mirrors\unittest\parse;

use lang\mirrors\parse\PhpSyntax;
use lang\mirrors\parse\CodeUnit;
use lang\mirrors\parse\Value;
use lang\mirrors\parse\TypeRef;
use lang\mirrors\parse\NewInstance;
use lang\mirrors\parse\Closure;
use lang\Primitive;

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

  #[@test]
  public function object_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Object', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php class Object { }')
    );
  }

  #[@test]
  public function interface_without_parent() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A { }')
    );
  }

  #[@test]
  public function interface_with_parent() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => ['B'], 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A extends B { }')
    );
  }

  #[@test]
  public function interface_with_multiple_parents() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'interface', 'comment' => null, 'parent' => null, 'implements' => ['B', 'C'], 'name' => 'A', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php interface A extends B, C { }')
    );
  }

  #[@test]
  public function runnable_impl() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => ['Runnable'], 'name' => 'Test', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php class Test implements Runnable { }')
    );
  }

  #[@test]
  public function creation_trait() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'trait', 'comment' => null, 'parent' => null, 'name' => 'Creation', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php trait Creation { }')
    );
  }

  #[@test]
  public function creation_user() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null, 'use' => [
        'Creation' => ['kind' => 'use', 'name' => 'Creation']
      ]]),
      $this->parse('<?php class Test { use Creation; }')
    );
  }

  #[@test]
  public function class_using_trait_with_alias() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null, 'use' => [
        'Creation' => ['kind' => 'use', 'name' => 'Creation']
      ]]),
      $this->parse('<?php class Test { use Creation { value as name; } }')
    );
  }

  #[@test]
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

  #[@test]
  public function abstract_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => ['abstract'], 'annotations' => null]),
      $this->parse('<?php abstract class Test { }')
    );
  }

  #[@test]
  public function final_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => null, 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => ['final'], 'annotations' => null]),
      $this->parse('<?php final class Test { }')
    );
  }

  #[@test]
  public function documented_class() {
    $this->assertEquals(
      new CodeUnit(null, [], ['kind' => 'class', 'comment' => '/** Doc */', 'parent' => null, 'implements' => null, 'name' => 'Test', 'modifiers' => [], 'annotations' => null]),
      $this->parse('<?php /** Doc */ class Test { }')
    );
  }

  #[@test]
  public function no_imports() {
    $this->assertEquals(
      [],
      $this->parse('<?php class Test { }')->imports()
    );
  }

  #[@test]
  public function one_import() {
    $this->assertEquals(
      ['Objects' => 'util\Objects'],
      $this->parse('<?php use util\Objects; class Test { }')->imports()
    );
  }

  #[@test]
  public function imports() {
    $this->assertEquals(
      ['Objects' => 'util\Objects', 'Date' => 'util\Date'],
      $this->parse('<?php use util\Objects; use util\Date; class Test { }')->imports()
    );
  }

  #[@test]
  public function aliased_import() {
    $this->assertEquals(
      ['Aliased' => 'util\Objects'],
      $this->parse('<?php use util\Objects as Aliased; class Test { }')->imports()
    );
  }

  #[@test]
  public function grouped_import() {
    $this->assertEquals(
      ['Date' => 'util\Date', 'DateUtil' => 'util\DateUtil'],
      $this->parse('<?php use util\{Date, DateUtil}; class Test { }')->imports()
    );
  }

  #[@test]
  public function new_import() {
    $this->assertEquals(
      ['ArrayListExtensions' => 'util\ArrayListExtensions'],
      $this->parse('<?php new import("util.ArrayListExtensions"); class Test { }')->imports()
    );
  }

  #[@test]
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

  #[@test]
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

  #[@test]
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

  #[@test]
  public function multi_line_annotation() {
    $parsed= $this->parse('<?php 
      #[@action(new \unittest\actions\VerifyThat(function() {
      #  throw new \lang\IllegalStateException("Test");
      #}))]
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