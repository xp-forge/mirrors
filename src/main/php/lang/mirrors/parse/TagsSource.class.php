<?php namespace lang\mirrors\parse;

use text\StringTokenizer;

/**
 * Source parser for tags in API documentation.
 *
 * @see  http://www.phpdoc.org/docs/latest/guides/types.html#keywords
 */
class TagsSource extends \text\parse\Tokens {
  const T_WORD     = 260;
  const T_PARSED   = 261;

  const T_FUNCTION = 270;
  const T_STRING   = 271;
  const T_BOOL     = 272;
  const T_INT      = 273;
  const T_DOUBLE   = 274;
  const T_VAR      = 275;
  const T_VOID     = 276;
  const T_CALLABLE = 277;
  const T_ARRAY    = 278;
  const T_THIS     = 279;
  const T_VARIADIC = 280;

  private static $keywords= [
    '@param'    => self::T_PARSED,
    '@throws'   => self::T_PARSED,
    '@return'   => self::T_PARSED,
    '@var'      => self::T_PARSED,
    '@type'     => self::T_PARSED,

    'function'  => self::T_FUNCTION,
    'string'    => self::T_STRING,
    'bool'      => self::T_BOOL,
    'int'       => self::T_INT,
    'double'    => self::T_DOUBLE,
    'var'       => self::T_VAR,
    'void'      => self::T_VOID,
    'callable'  => self::T_CALLABLE,
    'array'     => self::T_ARRAY,

    'float'     => self::T_DOUBLE,
    'integer'   => self::T_INT,
    'boolean'   => self::T_BOOL,

    '$this'     => self::T_THIS,
    'resource'  => self::T_VAR,
    'object'    => self::T_VAR,
    'mixed'     => self::T_VAR,
    'false'     => self::T_BOOL,
    'true'      => self::T_BOOL,
    'null'      => self::T_VOID,

    '...'       => self::T_VARIADIC,
    '*'         => self::T_VARIADIC
  ];

  /**
   * Creates a new tags source instance from a given input string
   *
   * @param  string $input
   */
  public function __construct($input) {
    $this->tokens= new StringTokenizer($input, "@\$:()<>[]|*, \t\n", true);
  }

  /** @return var */
  protected function next() {
    while ($this->tokens->hasMoreTokens()) {
      $token= $this->tokens->nextToken();
      if (strspn($token, ' ')) {
        continue;
      } else if ('@' === $token || '$' === $token) {
        $token.= $this->tokens->nextToken();
      } else if (0 === substr_compare($token, '...', -3)) {
        $token= substr($token, 0, -3);
        $this->tokens->pushBack('*');
      }

      if (1 === strlen($token)) {
        return $token;
      } else if (isset(self::$keywords[$token])) {
        return [self::$keywords[$token], $token];
      } else {
        return [self::T_WORD, $token];
      }
    }
    return null;
  }

  /**
   * Returns token name
   *
   * @param  string $token
   * @return string
   */
  protected function name($token) {
    return $token;    // Could be improved on.
  }
}