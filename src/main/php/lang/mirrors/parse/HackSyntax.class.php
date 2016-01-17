<?php namespace lang\mirrors\parse;

use text\parse\rules\Sequence;
use text\parse\rules\Token;
use text\parse\rules\Optional;
use text\parse\rules\Apply;
use text\parse\rules\Tokens;
use text\parse\rules\Repeated;
use text\parse\rules\Match;
use text\parse\rules\Returns;
use text\parse\rules\Collect;
use text\parse\rules\OneOf;
use lang\Type;
use lang\Primitive;

class HackSyntax extends PhpSyntax {

  /**
   * Extends base rules
   *
   * @param  [:text.parse.rules.Rules] $rules
   * @return [:text.parse.rules.Rules]
   */
  protected function extend($rules) {
    $rules['type']= new Match([
      T_STRING       => new Sequence([$this->typeName], function($values) {
        $t=  $values[0].implode('', $values[1]);
        if ('string' === $t) {
          return new TypeRef(Primitive::$STRING);
        } else if ('int' === $t) {
          return new TypeRef(Primitive::$INT);
        } else if ('double' === $t || 'float' === $t) {
          return new TypeRef(Primitive::$DOUBLE);
        } else if ('bool' === $t) {
          return new TypeRef(Primitive::$BOOL);
        } else if ('mixed' === $t) {
          return new TypeRef(Type::$VAR);
        } else if ('void' === $t) {
          return new TypeRef(Type::$VOID);
        } else if ('this' === $t) {
          return new ReferenceTypeRef('self');
        } else {
          return new ReferenceTypeRef($t);
        }
      }),
      T_NS_SEPARATOR => new Sequence([$this->typeName], function($values) { return new ReferenceTypeRef($values[0].implode('', $values[1])); } ),
      '('            => new Sequence(
        [new Token(T_FUNCTION), new Token('('), new Repeated(new Apply('type'), new Token(',')), new Token(')'), new Token(':'), new Apply('type'), new Token(')')],
        function($values) { return new FunctionTypeRef($values[3], $values[6]); }
      ),
      '?'            => new Sequence([new Apply('type')], function($values) { return $values[1]; }),
      T_ARRAY        => new Sequence(
        [new Optional(new Sequence(
          [new Tokens(398, '<'), new Repeated(new Apply('type'), new Token(',')), new Tokens(399, '>')],
          function($values) { return $values[1];}
        ))],
        function($values) {
          if (empty($values[1])) {
            return new TypeRef(Type::$ARRAY);
          } else if (1 === sizeof($values[1])) {
            return new ArrayTypeRef($values[1][0]);
          } else if (2 === sizeof($values[1])) {
            return new MapTypeRef($values[1][1]);
          }
        }
      ),
      T_CALLABLE     => new Returns(new TypeRef(Type::$CALLABLE)),
    ]);

    $rules['annotations']= new Match([
      '#' => new Sequence(
        [new Token('['), new Repeated(new Apply('annotation'), new Token(','), $this->collectAnnotations), new Token(']')],
        function($values) { return $values[2]; }
      ),
      T_SL => new Sequence(
        [new Repeated(new Apply('attribute'), new Token(','), $this->collectAnnotations), new Token(T_SR)],
        function($values) { return $values[1]; }
      ),
    ]);
    $rules['attribute']= new Sequence(
      [new Token(T_STRING), new Optional(new Apply('value'))],
      function($values) { return ['target' => [null, $values[0]], 'value' => $values[1]]; }
    );
    return $rules;
  }
}