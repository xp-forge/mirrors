<?php namespace lang\mirrors\unittest;

use lang\mirrors\TypeMirror;
use lang\Type;

class InvalidTypesTest extends \unittest\TestCase {
  use TypeDefinition;

  /** @return var[][] */
  private function invalidDeclarations() {
    return [
      [''],
      ['function'],
      ['()'],
      ['|'],
      ['[]'],
      ['<string>'],
      ['<string, string>[]']
    ];
  }

  #[@test, @values('invalidDeclarations')]
  public function invalid_return_type($declaration) {
    $mirror= $this->mirror('{ /** @return '.$declaration.' */ public function fixture() { } }');
    $this->assertEquals(Type::$VAR, $mirror->method('fixture')->returns());
  }

  #[@test, @values('invalidDeclarations')]
  public function invalid_parametern_type($declaration) {
    $mirror= $this->mirror('{ /** @param '.$declaration.' */ public function fixture($param) { } }');
    $this->assertEquals(Type::$VAR, $mirror->method('fixture')->parameter('param')->type());
  }
}