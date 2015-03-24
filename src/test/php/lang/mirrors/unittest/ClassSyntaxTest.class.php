<?php namespace lang\mirrors\unittest;

use lang\mirrors\parse\ClassSyntax;
use lang\mirrors\parse\CodeUnit;

/**
 * Tests ClassSyntax
 */
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
      new CodeUnit(null, [], ['kind' => 'class', 'name' => 'Object', 'annotations' => null]),
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
            'can_create' => [
              'kind'        => 'method',
              'name'        => 'can_create',
              'params'      => [],
              'access'      => ['public'],
              'annotations' => ['test' => null]
            ]
          ]
        ]
      ),
      $this->parse('<?php namespace de\thekid\test;

        use util\Objects;

        class IntegrationTest extends \unittest\TestCase {
          private $fixture;

          #[@test]
          public function can_create() { /* ... */ }
        }
      ')
    );
  }
}