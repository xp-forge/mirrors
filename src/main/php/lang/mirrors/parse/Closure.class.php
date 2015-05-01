<?php namespace lang\mirrors\parse;

use util\Objects;
use lang\IllegalStateException;

class Closure extends \lang\Object {
  private $params, $code;

  public function __construct($params, $code) {
    $this->params= $params;
    $this->code= $code;
  }

  public function resolve($unit) {
    $signature= '';
    foreach ($this->params as $param) {
      $signature.= sprintf(
        ', %s%s$%s%s',
        $param['type'],
        $param['ref'] ? ' &' : ' ',
        $param['name'],
        $param['default'] ? '= '.var_export($param['default']->resolve($unit), 1) : ''
      );
    }
    $func= eval('return function('.substr($signature, 2).') {'.$this->code.'};');
    if (!($func instanceof \Closure)) {
      if ($error= error_get_last()) {
        set_error_handler('__error', 0);
        trigger_error('clear_last_error');
        restore_error_handler();
      } else {
        $error= ['message' => 'Syntax error'];
      }
      throw new IllegalStateException('In `'.$this->code.'`: '.ucfirst($error['message']));
    }
    return $func;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'<('.Objects::stringOf($this->params).')'.$this->code.'>';
  }

  /**
   * Returns whether a given value is equal to this code unit
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      $this->code === $cmp->code &&
      $this->params === $cmp->params
    );
  }
}