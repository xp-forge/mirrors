<?php namespace lang\mirrors\parse;

use text\parse\Rules;
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

class ClassSyntax extends \text\parse\Syntax {

  /** @return text.parse.Rules */
  protected function rules() {
    $typeName= new Tokens(T_STRING, T_NS_SEPARATOR);
    $collectMembers= newinstance('text.parse.rules.Collection', [], '{
      public function collect(&$values, $value) {
        $values[$value["kind"]][$value["name"]]= $value;
      }
    }');
    $collectElements= newinstance('text.parse.rules.Collection', [], '{
      public function collect(&$values, $value) {
        if (is_array($value)) {
          $values[key($value)]= current($value);
        } else {
          $values[]= $value;
        }
      }
    }');
    $collectAnnotations= newinstance('text.parse.rules.Collection', [], '{
      public function collect(&$values, $value) {
        $target= $value["target"];
        $values[$target[0]][$target[1]]= $value["value"];
      }
    }');

    return new Rules([
      new Sequence([new Token(T_OPEN_TAG), new Optional(new Apply('package')), new Repeated(new Apply('import')), new Apply('decl')], function($values) {
        return new CodeUnit($values[1], $values[2], $values[3]);
      }),
      'package' => new Sequence([new Token(T_NAMESPACE), $typeName, new Token(';')], function($values) {
        return strtr(implode('', $values[1]), '\\', '.');
      }),
      'import' => new Match([
        T_USE => new Sequence([$typeName, new Token(';')], function($values) {
          return strtr(implode('', $values[1]), '\\', '.');
        }),
        T_NEW => new Sequence([new Token(T_STRING), new Token('('), new Token(T_CONSTANT_ENCAPSED_STRING), new Token(')'), new Token(';')], function($values) {
          return trim($values[3], '\'"');
        })
      ]),
      'decl' => new Sequence(
        [
          new Optional(new Apply('annotations')),
          new Match([
            T_CLASS     => new Sequence([new Token(T_STRING), new Tokens(T_STRING, T_NS_SEPARATOR, T_EXTENDS, T_IMPLEMENTS), new Apply('type')], function($values) {
              return array_merge(['kind' => $values[0], 'name' => $values[1]], $values[3]);
            }),
            T_INTERFACE => new Returns(T_INTERFACE),
            T_TRAIT     => new Returns(T_TRAIT)
          ])
        ],
        function($values) { return array_merge($values[1], ['annotations' => $values[0]]); }
      ),
      'annotations' => new Sequence(
        [new Token('['), new Repeated(new Apply('annotation'), new Token(','), $collectAnnotations), new Token(']')],
        function($values) { return $values[1]; }
      ),
      'annotation' => new Sequence(
        [new Token('@'), new Apply('annotation_target'), new Optional(new Apply('value'))],
        function($values) { return ['target' => $values[1], 'value' => $values[2]]; }
      ),
      'annotation_target' => new Match([
        T_STRING   => new Returns(function($values) { return [null, $values[0]]; }),
        T_VARIABLE => new Sequence([new Token(':'), new Token(T_STRING)], function($values) { return [$values[0], $values[2]]; })
      ]),
      'value' => new Sequence(
        [new Token('('), new Apply('expr'), new Token(')')],
        function($values) { return $values[1]; }
      ),
      'type' => new Sequence(
        [new Token('{'), new Repeated(new Apply('member'), null, $collectMembers), new Token('}')],
        function($values) { return $values[1]; }
      ),
      'member' => new OneOf([
        new Match([
          T_CONST => new Sequence([new Token(T_STRING), new Token('='), new Apply('expr'), new Token(';')], function($values) {
            return ['kind' => 'const', 'name' => $values[1]];
          })
        ]),
        new Sequence(
          [
            new Optional(new Apply('annotations')),
            new Apply('modifiers'),
            new Match([
              T_FUNCTION => new Sequence(
                [new Token(T_STRING), new Token('('), new Repeated(new Apply('param'), new Token(',')), new Token(')'), new Apply('method')],
                function($values) { return ['kind' => 'method', 'name' => $values[1], 'params' => $values[3]]; }
              ),
              T_VARIABLE => new Sequence(
                [new Optional(new Sequence([new Token('='), new Apply('expr')], function($values) { return $values[1]; })), new Match([',' => null, ';' => null])],
                function($values) { return ['kind' => 'field', 'name' => $values[0], 'init' => $values[2]]; }
              ),
            ])
          ],
          function($values) { return array_merge($values[2], ['access' => $values[1], 'annotations' => $values[0]]); }
        ),
      ]),
      'modifiers' => new Tokens(T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC, T_FINAL, T_ABSTRACT),
      'param' => new Sequence(
        [
          new Tokens(T_ARRAY, T_CALLABLE, T_STRING, T_NS_SEPARATOR, T_ELLIPSIS),
          new Optional(new Token('&')),
          new Token(T_VARIABLE),
          new Optional(new Sequence([new Token('='), new Apply('expr')], function($values) { return $values[1]; }))
        ],
        function($values) { return ['name' => $values[2], 'type' => $values[0] ? implode('', $values[0]) : null, 'ref' => isset($values[1]), 'default' => $values[3]]; }
      ),
      'method' => new Match([';' => null, '{' => new Block(true)]),
      'expr' => new OneOf([
        new Match([
          T_DNUMBER => function($values) { return new Value((double)$values[0]); },
          T_LNUMBER => function($values) { return new Value((int)$values[0]); },
          '-' => new Match([
            T_DNUMBER => function($values) { return new Value(-(double)$values[0]); },
            T_LNUMBER => function($values) { return new Value(-(int)$values[0]); },
          ]),
          '+' => new Match([
            T_DNUMBER => function($values) { return new Value((double)$values[0]); },
            T_LNUMBER => function($values) { return new Value((int)$values[0]); },
          ]),
          T_CONSTANT_ENCAPSED_STRING => function($values) { return new Value(eval('return '.$values[0].';')); },
          '[' => new Sequence([new Repeated(new Apply('element'), new Token(','), $collectElements), new Token(']')], function($values) {
            return new ArrayExpr($values[1]);
          }),
          T_ARRAY => new Sequence([new Token('('), new Repeated(new Apply('element'), new Token(','), $collectElements), new Token(')')], function($values) {
            return new ArrayExpr($values[2]);
          }),
          T_FUNCTION => new Sequence([new Token('('), new Repeated(new Apply('param'), new Token(',')), new Token(')'), new Block(false)], function($values) {
            return new Closure($values[2], $values[4]);
          }),
          T_NEW => new Sequence([$typeName, new Token('('), new Repeated(new Apply('expr'), new Token(',')), new Token(')')], function($values) {
            return new NewInstance(implode('', $values[1]), (array)$values[3]);
          }),
        ]),
        new Sequence([$typeName, new Token(T_DOUBLE_COLON), new Apply('member_ref')], function($values) {
          return new Member(implode('', $values[0]), $values[2]);
        }),
        new Sequence([new Apply('pair'), new Optional(new Token(',')), new Repeated(new Apply('pair'), new Token(','), Collect::$AS_MAP)], function($values) {
          return new Pairs(array_merge($values[0], $values[2]));
        }),
        new Sequence([new Token(T_STRING)], function($values) {
          return new Constant($values[0]);
        })
      ]),
      'element' => new OneOf([
        new Sequence([new Token(T_CONSTANT_ENCAPSED_STRING), new Token(T_DOUBLE_ARROW), new Apply('expr')], function($values) {
          return [trim($values[0], '"\'') => $values[2]];
        }),
        new Sequence([new Apply('expr')], function($values) {
          return $values[0];
        })
      ]),
      'pair'  => new Sequence([new Apply('key'), new Token('='), new Apply('expr')], function($values) {
        return [$values[0] => $values[2]];
      }),
      'key' => new Match([
        T_STRING      => new Returns(function($values) { return $values[0]; }),
        T_RETURN      => new Returns('return'),
        T_CLASS       => new Returns('class'),
        T_IMPLEMENTS  => new Returns('implements'),
        T_LIST        => new Returns('list')
      ]),
      'member_ref' => new Match([
        T_STRING   => function($values) { return $values[0]; },
        T_CLASS    => function($values) { return $values[0]; },
        T_VARIABLE => function($values) { return $values[0]; },
      ])
    ]);
  }
}