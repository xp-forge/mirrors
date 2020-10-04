<?php namespace lang\mirrors\parse;

use lang\{Primitive, Type};
use text\parse\rules\{Apply, Collect, Collection, Matches, OneOf, Optional, Repeated, Returns, Sequence, Token, Tokens};
use text\parse\{Rules, Syntax};

class PhpSyntax extends Syntax {
  protected $typeName, $collectMembers, $collectElements, $collectAnnotations;

  static function __static() {
    defined('T_ELLIPSIS') ||define('T_ELLIPSIS', -389);
    defined('T_FN') || define('T_FN', -346);
    defined('T_ATTRIBUTE') || define('T_ATTRIBUTE', -383);
    defined('T_NAME_FULLY_QUALIFIED') || define('T_NAME_FULLY_QUALIFIED', -312);
    defined('T_NAME_QUALIFIED') || define('T_NAME_QUALIFIED', -314);
  }

  /**
   * Initialize members.
   */
  public function __construct() {
    $this->typeName= new Tokens(T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED);
    $this->collectMembers= new class() implements Collection {
      public function collect(&$values, $value) {
        $values[$value['kind']][$value['name']]= $value;
      }
    };
    $this->collectElements= new class() implements Collection {
      public function collect(&$values, $value) {
        if (is_array($value)) {
          $values[key($value)]= current($value);
        } else {
          $values[]= $value;
        }
      }
    };
    $this->collectAnnotations= new class() implements Collection {
      public function collect(&$values, $value) {
        $target= $value['target'];
        $values[$target[0]][$target[1]]= $value['value'];
      }
    };
    $this->collectImports= new class() implements Collection {
      public function collect(&$values, $value) {
        foreach ($value as $local => $qualified) {
          $values[$local]= $qualified;
        }
      }
    };
    parent::__construct();
  }

  /**
   * Extends base rules
   *
   * @param  [:text.parse.rules.Rules] $rules
   * @return [:text.parse.rules.Rules]
   */
  protected function extend($rules) {
    return $rules;
  }

  /** @return text.parse.Rules */
  protected function rules() {
    return new Rules($this->extend([
      new Sequence([new Optional(new Apply('package')), new Repeated(new Apply('import'), null, $this->collectImports), new Apply('decl')], function($values) {
        return new CodeUnit($values[0], $values[1], $values[2]);
      }),
      'package' => new Sequence([new Token(T_NAMESPACE), $this->typeName, new Token(';')], function($values) {
        return implode('', $values[1]);
      }),
      'import' => new Matches([
        T_USE => new Sequence([$this->typeName, new Matches([
          T_AS => new Sequence([new Token(T_STRING), new Token(';')], function($values) { return $values[1]; }),
          '{'  => new Sequence([new Repeated($this->typeName, new Token(',')), new Token('}'), new Token(';')], function($values) { return $values[1]; }),
          ';'  => new Returns(null),
        ])], function($values) {
          if (null === $values[2]) {
            $name= implode('', $values[1]);
            $p= strrpos($name, '\\');
            return [false === $p ? $name : substr($name, $p + 1) => $name];
          } else if (is_array($values[2])) {
            $return= [];
            foreach ($values[2] as $type) {
              $local= implode('', $type);
              $return[$local]= implode('', $values[1]).$local;
            }
            return $return;
          } else {
            return [$values[2] => implode('', $values[1])];
          }
        }),
        T_NEW => new Sequence([new Token(T_STRING), new Token('('), new Token(T_CONSTANT_ENCAPSED_STRING), new Token(')'), new Token(';')], function($values) {
          $name= strtr(trim($values[3], '\'"'), '.', '\\');
          $p= strrpos($name, '\\');
          return [false === $p ? $name : substr($name, $p + 1) => $name];
        })
      ]),
      'type' => new Matches([
        T_STRING       => new Sequence([$this->typeName], function($values) {
          $t= $values[0].implode('', $values[1]);
          if ('string' === $t) {
            return new TypeRef(Primitive::$STRING);
          } else if ('int' === $t) {
            return new TypeRef(Primitive::$INT);
          } else if ('double' === $t || 'float' === $t) {
            return new TypeRef(Primitive::$DOUBLE);
          } else if ('bool' === $t) {
            return new TypeRef(Primitive::$BOOL);
          } else {
            return new ReferenceTypeRef($t);
          }
        }),
        T_NS_SEPARATOR => new Sequence([$this->typeName], function($values) { return new ReferenceTypeRef($values[0].implode('', $values[1])); } ),
        T_ARRAY        => new Returns(new TypeRef(Type::$ARRAY)),
        T_CALLABLE     => new Returns(new TypeRef(Type::$CALLABLE)),
      ]),
      'decl' => new Sequence(
        [
          new Returns(function($values, $source) { return $source->lastComment(); }),
          new Optional(new Apply('annotations')),
          new Apply('modifiers'),
          new Matches([
            T_CLASS     => new Sequence([new Token(T_STRING), new Optional(new Apply('parent')), new Optional(new Apply('implements')), new Apply('body')], function($values) {
              return array_merge(['kind' => $values[0], 'name' => $values[1], 'parent' => $values[2], 'implements' => $values[3]], $values[4]);
            }),
            T_INTERFACE => new Sequence([new Token(T_STRING), new Optional(new Apply('parents')), new Apply('body')], function($values) {
              return array_merge(['kind' => $values[0], 'name' => $values[1], 'parent' => null, 'implements' => $values[2]], $values[3]);
            }),
            T_TRAIT     => new Sequence([new Token(T_STRING), new Apply('body')], function($values) {
              return array_merge(['kind' => $values[0], 'name' => $values[1], 'parent' => null], $values[2]);
            }),
          ])
        ],
        function($values) { return array_merge($values[3], ['comment' => $values[0], 'modifiers' => $values[2], 'annotations' => $values[1]]); }
      ),
      'parent' => new Sequence(
        [new Token(T_EXTENDS), $this->typeName],
        function($values) { return implode('', $values[1]); }
      ),
      'parents' => new Sequence(
        [new Token(T_EXTENDS), new Repeated($this->typeName, new Token(','))],
        function($values) { return array_map(function($e) { return implode('', $e); }, $values[1]); }
      ),
      'implements' => new Sequence(
        [new Token(T_IMPLEMENTS), new Repeated($this->typeName, new Token(','))],
        function($values) { return array_map(function($e) { return implode('', $e); }, $values[1]); }
      ),
      'annotations' => new Sequence(
        [
          new Matches(['#' => new Token('['), T_ATTRIBUTE => new Returns(null)]),
          new Repeated(new Apply('annotation'), new Token(','), $this->collectAnnotations),
          new Token(']')
        ],
        function($values) { isset($values[1][null]) || $values[1][null]= []; return $values[1]; }
      ),
      'annotation' => new Matches([
        '@'      => new Sequence([new Apply('annotation_target'), new Optional(new Apply('value'))], function($values) {
          return ['target' => $values[1], 'value' => $values[2]];
        }),
        T_STRING => new Sequence([new Optional(new Apply('attribute'))], function($values) {
          return ['target' => [null, lcfirst($values[0])], 'value' => $values[1]];
        })
      ]),
      'annotation_target' => new Matches([
        T_STRING   => new Returns(function($values) { return [null, $values[0]]; }),
        T_VARIABLE => new Sequence([new Token(':'), new Token(T_STRING)], function($values) { return [$values[0], $values[2]]; })
      ]),
      'value' => new Sequence(
        [new Token('('), new Apply('expr'), new Token(')')],
        function($values) { return $values[1]; }
      ),
      'attribute' => new Sequence(
        [new Token('('), new Repeated(new Apply('argument'), new Token(',')), new Token(')')],
        function($values) {
          $arguments= [];
          foreach ($values[1] as $argument) {
            if (null === $argument[0]) {
              $arguments[]= $argument[1];
            } else {
              $arguments[$argument[0]]= $argument[1];
            }
          }

          // Map arguments to value. Handle special key "eval"
          if (0 === key($arguments)) {
            return $arguments[0];
          } else if ('eval' === key($arguments)) {
            $rules= $this->rules();
            return $rules->named('expr')->evaluate($rules, new StringSource($arguments['eval']->resolve(null)));
          } else {
            return new Pairs($arguments);
          }
        }
      ),
      'argument' => new OneOf([
        new Sequence([new Apply('key'), new Token(':'), new Apply('expr')], function($values) {
          return [$values[0], $values[2]];
        }),
        new Sequence([new Apply('expr')], function($values) {
          return [null, $values[0]];
        })
      ]),
      'body' => new Sequence(
        [new Token('{'), new Repeated(new Apply('member'), null, $this->collectMembers), new Token('}')],
        function($values) { return $values[1]; }
      ),
      'member' => new OneOf([
        new Matches([
          T_USE   => new Sequence([$this->typeName, new Apply('aliases')], function($values) {
            return ['kind' => 'use', 'name' => implode('', $values[1])];
          }),
          T_CONST => new Sequence([new Token(T_STRING), new Token('='), new Apply('expr'), new Token(';')], function($values) {
            return ['kind' => 'const', 'name' => $values[1], 'value' => $values[3]];
          })
        ]),
        new Sequence(
          [
            new Returns(function($values, $source) { return $source->lastComment(); }),
            new Optional(new Apply('annotations')),
            new Apply('modifiers'),
            new OneOf([
              new Matches([
                T_FUNCTION => new Sequence(
                  [
                    new Token(T_STRING),
                    new Token('('), new Repeated(new Apply('param'), new Token(',')), new Token(')'),
                    new Optional(new Sequence([new Token(':'), new Apply('type')], function($values) { return $values[1]; })),
                    new Apply('method')
                  ],
                  function($values) { return ['kind' => 'method', 'name' => $values[1], 'params' => $values[3], 'returns' => $values[5]]; }
                ),
                T_VARIABLE => new Sequence(
                  [new Optional(new Apply('init')), new Matches([',' => null, ';' => null])],
                  function($values) { return ['kind' => 'field', 'name' => substr($values[0], 1), 'init' => $values[2]]; }
                ),
              ]),
              new Sequence(
                [new Apply('type'), new Token(T_VARIABLE), new Optional(new Apply('init')), new Matches([',' => null, ';' => null])],
                function($values) { return ['kind' => 'field', 'name' => substr($values[1], 1), 'init' => $values[2], 'type' => $values[0]]; }
              )
            ])
          ],
          function($values) { return array_merge($values[3], ['comment' => $values[0], 'access' => $values[2], 'annotations' => $values[1]]); }
        ),
      ]),
      'init' => new Sequence([new Token('='), new Apply('expr')], function($values) { return $values[1]; }),
      'modifiers' => new Tokens(T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC, T_FINAL, T_ABSTRACT),
      'param' => new Sequence(
        [
          new Optional(new Apply('annotations')),
          new Apply('modifiers'),
          new Optional(new Apply('type')),
          new Optional(new Token(T_ELLIPSIS)),
          new Optional(new Token('&')),
          new Token(T_VARIABLE),
          new Optional(new Apply('init'))
        ],
        function($values) { return [
          'name'        => substr($values[5], 1),
          'annotations' => $values[0],
          'type'        => $values[2],
          'ref'         => isset($values[4]),
          'var'         => isset($values[3]),
          'this'        => $values[1],
          'default'     => $values[6]
        ]; }
      ),
      'aliases' => new Matches([';' => null, '{' => new Block(true)]), 
      'method' => new Matches([';' => null, '{' => new Block(true)]),
      'expr' => new OneOf([
        new Matches([
          T_DNUMBER => function($values) { return new Value((double)$values[0]); },
          T_LNUMBER => function($values) { return new Value((int)$values[0]); },
          '-' => new Matches([
            T_DNUMBER => function($values) { return new Value(-(double)$values[0]); },
            T_LNUMBER => function($values) { return new Value(-(int)$values[0]); },
          ]),
          '+' => new Matches([
            T_DNUMBER => function($values) { return new Value((double)$values[0]); },
            T_LNUMBER => function($values) { return new Value((int)$values[0]); },
          ]),
          T_CONSTANT_ENCAPSED_STRING => function($values) { return new Value(eval('return '.$values[0].';')); },
          '[' => new Sequence([new Repeated(new Apply('element'), new Token(','), $this->collectElements), new Token(']')], function($values) {
            return new ArrayExpr($values[1]);
          }),
          T_ARRAY => new Sequence([new Token('('), new Repeated(new Apply('element'), new Token(','), $this->collectElements), new Token(')')], function($values) {
            return new ArrayExpr($values[2]);
          }),
          T_FUNCTION => new Sequence([new Token('('), new Repeated(new Apply('param'), new Token(',')), new Token(')'), new Block(false)], function($values) {
            return new Closure($values[2], $values[4]);
          }),
          T_NEW => new Sequence([$this->typeName, new Token('('), new Repeated(new Apply('expr'), new Token(',')), new Token(')')], function($values) {
            return new NewInstance(implode('', $values[1]), (array)$values[3]);
          }),
        ]),
        new Sequence([$this->typeName, new Token(T_DOUBLE_COLON), new Apply('member_ref')], function($values) {
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
      'key' => new Matches([
        T_STRING      => new Returns(function($values) { return $values[0]; }),
        T_AS          => new Returns('as'),
        T_BREAK       => new Returns('break'),
        T_CASE        => new Returns('case'),
        T_CALLABLE    => new Returns('callable'),
        T_CATCH       => new Returns('catch'),
        T_CLASS       => new Returns('class'),
        T_CLONE       => new Returns('clone'),
        T_CONST       => new Returns('const'),
        T_CONTINUE    => new Returns('continue'),
        T_DECLARE     => new Returns('declare'),
        T_DEFAULT     => new Returns('default'),
        T_DO          => new Returns('do'),
        T_ELSE        => new Returns('else'),
        T_EVAL        => new Returns('eval'),
        T_EXTENDS     => new Returns('extends'),
        T_FINALLY     => new Returns('finally'),
        T_FOR         => new Returns('for'),
        T_FOREACH     => new Returns('foreach'),
        T_FUNCTION    => new Returns('function'),
        T_GLOBAL      => new Returns('global'),
        T_GOTO        => new Returns('goto'),
        T_IF          => new Returns('if'),
        T_IMPLEMENTS  => new Returns('implements'),
        T_INCLUDE     => new Returns('include'),
        T_INSTANCEOF  => new Returns('instanceof'),
        T_INSTEADOF   => new Returns('insteadof'),
        T_INTERFACE   => new Returns('interface'),
        T_LIST        => new Returns('list'),
        T_NAMESPACE   => new Returns('namespace'),
        T_NEW         => new Returns('new'),
        T_REQUIRE     => new Returns('require'),
        T_RETURN      => new Returns('return'),
        T_SWITCH      => new Returns('switch'),
        T_THROW       => new Returns('throw'),
        T_TRAIT       => new Returns('trait'),
        T_TRY         => new Returns('try'),
        T_USE         => new Returns('use'),
        T_VAR         => new Returns('var'),
        T_WHILE       => new Returns('while'),
        T_YIELD       => new Returns('yield')
      ]),
      'member_ref' => new Matches([
        T_STRING   => function($values) { return $values[0]; },
        T_CLASS    => function($values) { return $values[0]; },
        T_VARIABLE => function($values) { return $values[0]; },
      ])
    ]));
  }
}