<?php namespace lang\mirrors\parse;

use lang\{Primitive, Type};
use text\parse\Rules;
use text\parse\rules\{Apply, Collect, Matches, OneOf, Optional, Repeated, Returns, Sequence, Token};

class TagsSyntax extends \text\parse\Syntax {

  /** @return text.parse.Rules */
  protected function rules() {
    return new Rules([
      new Repeated(
        new Apply('tag', function($values) { return $values[0]; }),
        new Token("\n"),
        newinstance('text.parse.rules.Collection', [], '{
          public function collect(&$values, $value) {
            $values[key($value)][]= current($value);
          }
        }')
      ),
      'tag' => new Matches([
        TagsSource::T_PARSED => new Sequence(
          [new Apply('types'), new Text()],
          function($values) { return [substr($values[0], 1) => $values[1]]; }
        ),
        TagsSource::T_WORD => new Sequence(
          [new Text()],
          function($values) { return [substr($values[0], 1) => $values[1]]; }
        )
      ]),
      'types' => new Sequence(
        [new Repeated(new Apply('type'), new Token('|'))],
        function($values) {
          if (empty($values[0])) {
            return null;
          } else if (1 === sizeof($values[0])) {
            return $values[0][0];
          } else {
            return new TypeUnionRef($values[0]);
          }
        }
      ),
      'type' => new Sequence(
        [
          new Matches([
            TagsSource::T_STRING   => new Returns(new TypeRef(Primitive::$STRING)),
            TagsSource::T_DOUBLE   => new Returns(new TypeRef(Primitive::$DOUBLE)),
            TagsSource::T_INT      => new Returns(new TypeRef(Primitive::$INT)),
            TagsSource::T_BOOL     => new Returns(new TypeRef(Primitive::$BOOL)),
            TagsSource::T_VAR      => new Returns(new TypeRef(Type::$VAR)),
            TagsSource::T_VOID     => new Returns(new TypeRef(Type::$VOID)),
            TagsSource::T_CALLABLE => new Returns(new TypeRef(Type::$CALLABLE)),
            TagsSource::T_ARRAY    => new Returns(new TypeRef(Type::$ARRAY)),
            TagsSource::T_ITERABLE => new Returns(new TypeRef(property_exists(Type::class, 'ITERABLE') ? Type::$ITERABLE : Type::$VAR)),
            TagsSource::T_OBJECT   => new Returns(new TypeRef(property_exists(Type::class, 'OBJECT') ? Type::$OBJECT : Type::$VAR)),
            TagsSource::T_THIS     => new Returns(new ReferenceTypeRef('self')),
            TagsSource::T_FUNCTION => new Sequence(
              [new Token('('), new Repeated(new Apply('types'), new Token(',')), new Token(')'), new Token(':'), new Apply('type')],
              function($values) { return new FunctionTypeRef([null] === $values[2] ? [] : $values[2], $values[5]); }
            ),
            TagsSource::T_WORD     => new Sequence(
              [new Optional(new Sequence(
                [new Token('<'), new Repeated(new Apply('types'), new Token(',')), new Token('>')],
                function($values) { return $values[1]; }
              ))],
              function($values) {
                $base= new ReferenceTypeRef($values[0]);
                return $values[1] ? new GenericTypeRef($base, $values[1]) : $base;
              }
            ),
            '(' => new Sequence(
              [new Apply('types'), new Token(')')],
              function($values) { return $values[1]; }
            ),
            '[' => new Sequence(
              [new Token(':'), new Apply('types'), new Token(']')],
              function($values) { return new MapTypeRef($values[2]); }
            )
          ]),
          new Repeated(new Sequence([new Token('['), new Token(']')], function() { return true; })),
          new Optional(new Sequence([new Token('*')], function() { return true; }))
        ],
        function($values) {
          $return= $values[0];
          foreach ($values[1] as $do) { $return= new ArrayTypeRef($return); }
          if (isset($values[2])) { $return= new VariadicTypeRef($return); }
          return $return;
        }
      )
    ]);
  }
}