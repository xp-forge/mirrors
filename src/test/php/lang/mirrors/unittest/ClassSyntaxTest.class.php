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
      new CodeUnit(null, [], ['kind' => 'class', 'parent' => null, 'name' => 'Object', 'annotations' => null]),
      $this->parse('<?php class Object { }')
    );
  }

  #[@test]
  public function test_class() {
    $this->assertEquals(
      new CodeUnit(
        'de.thekid.test',
        ['util.Objects'],
        [
          'kind'        => 'class',
          'name'        => 'IntegrationTest',
          'parent'      => '\unittest\TestCase',
          'annotations' => null,
          'field' => [
            '$fixture' => [
              'kind'        => 'field',
              'name'        => '$fixture',
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
                'name'    => '$arg',
                'type'    => null,
                'ref'     => false,
                'default' => null,
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
}