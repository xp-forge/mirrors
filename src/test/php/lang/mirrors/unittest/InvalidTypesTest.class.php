<?php namespace lang\mirrors\unittest;

use lang\Type;
use lang\mirrors\TypeMirror;
use unittest\{Test, Values};

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

  #[Test, Values('invalidDeclarations')]
  public function invalid_return_type($declaration) {
    $mirror= $this->mirror('{ /** @return '.$declaration.' */ public function fixture() { } }');
    $this->assertEquals(Type::$VAR, $mirror->method('fixture')->returns());
  }

  #[Test, Values('invalidDeclarations')]
  public function invalid_parametern_type($declaration) {
    $mirror= $this->mirror('{ /** @param '.$declaration.' */ public function fixture($param) { } }');
    $this->assertEquals(Type::$VAR, $mirror->method('fixture')->parameter('param')->type());
  }
}