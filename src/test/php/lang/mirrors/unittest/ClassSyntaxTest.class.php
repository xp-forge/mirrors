<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\CodeUnit;
use lang\mirrors\parse\Value;

class ClassSyntaxTest extends \unittest\TestCase {

  /**
   * Parses a string
   *
   * @param  string $input
   * @return lang.reflection.parse.CodeUnit
   */
  private function parse($input) {
    return (new ClassSyntax())->parse(new StringInput($input));
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
  public function test_class() {
    $this->assertEquals(
      new CodeUnit(
        'de\thekid\test',
        ['util\Objects'],
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
              'annotations' => null
            ]
          ],
          'method' => [
            'connect' => [
              'kind'        => 'method',
              'name'        => 'connect',
              'params'      => [[
                'name'    => 'arg',
                'type'    => null,
                'ref'     => false,
                'var'     => false,
                'default' => null
              ]],
              'access'      => ['private'],
              'annotations' => ['$arg' => ['inject' => new Value('db')]]
            ],
            'can_create' => [
              'kind'        => 'method',
              'name'        => 'can_create',
              'params'      => [],
              'access'      => ['public'],
              'annotations' => [null => ['test' => null]]
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
          'annotations' => null
        ],
        'b' => [
          'kind'        => 'field',
          'name'        => 'b',
          'init'        => null,
          'access'      => [],
          'annotations' => null
        ]
      ],
      $this->parse('<?php class Test { private $a, $b; }')->declaration()['field']
    );
  }
}