<?php namespace lang\mirrors\parse;

use text\parse\Rules;
use text\parse\rules\Repeated;
use text\parse\rules\Sequence;
use text\parse\rules\Token;
use text\parse\rules\Apply;
use text\parse\rules\Match;
use text\parse\rules\OneOf;
use text\parse\rules\Optional;
use text\parse\rules\Collect;
use text\parse\rules\Returns;
use lang\Primitive;
use lang\Type;

class TagsSyntax extends \text\parse\Syntax {

  /** @return text.parse.Rules */
  protected function rules() {
    return new Rules([
      new Repeated(
        new Sequence([new Token('@'), new Apply('tag')], function($values) { return $values[1]; }),
        new Token("\n"),
        newinstance('text.parse.rules.Collection', [], '{
          public function collect(&$values, $value) {
            $values[key($value)][]= current($value);
          }
        }')
      ),
      'tag' => new Match([
        TagsSource::T_PARSED => new Sequence(
          [new Apply('type'), new Text()],
          function($values) { return [$values[0] => $values[1]]; }
        ),
        TagsSource::T_WORD => new Sequence(
          [new Text()],
          function($values) { return [$values[0] => $values[1]]; }
        )
      ]),
      'braces' => new Match([
        TagsSource::T_FUNCTION => new Sequence(
          [new Token('('), new Repeated(new Apply('type'), new Token(',')), new Token(')'), new Token(':'), new Apply('type')],
          function($values) { return new FunctionTypeRef($values[2], $values[5]); }
        )
      ]),
      'type' => new Sequence(
        [
          new OneOf([
            new Match([
              TagsSource::T_STRING   => new Returns(new TypeRef(Primitive::$STRING)),
              TagsSource::T_DOUBLE   => new Returns(new TypeRef(Primitive::$DOUBLE)),
              TagsSource::T_INT      => new Returns(new TypeRef(Primitive::$INT)),
              TagsSource::T_BOOL     => new Returns(new TypeRef(Primitive::$BOOL)),
              TagsSource::T_VAR      => new Returns(new TypeRef(Type::$VAR)),
              TagsSource::T_VOID     => new Returns(new TypeRef(Type::$VOID)),
              TagsSource::T_CALLABLE => new Returns(new TypeRef(Type::$CALLABLE)),
              TagsSource::T_ARRAY    => new Returns(new TypeRef(Type::$ARRAY)),
              TagsSource::T_WORD     => new Sequence(
                [new Optional(new Sequence(
                  [new Token('<'), new Repeated(new Apply('type'), new Token(',')), new Token('>')],
                  function($values) { return $values[1]; }
                ))],
                function($values) {
                  $base= new ReferenceTypeRef($values[0]);
                  return $values[1] ? new GenericTypeRef($base, $values[1]) : $base;
                }
              ),
              '(' => new Sequence(
                [new Apply('braces'), new Token(')')],
                function($values) { return $values[1]; }
              ),
              '[' => new Sequence(
                [new Token(':'), new Apply('type'), new Token(']')],
                function($values) { return new MapTypeRef($values[2]); }
              )
            ]),
            new Apply('braces')
          ]),
          new Repeated(new Sequence([new Token('['), new Token(']')], function() { return true; })),
        ],
        function($values) {
          $return= $values[0];
          foreach ($values[1] as $do) {
            $return= new ArrayTypeRef($return);
          }
          return $return;
        }
      )
    ]);
  }
}