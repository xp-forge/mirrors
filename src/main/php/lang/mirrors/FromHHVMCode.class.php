<?php namespace lang\mirrors;

use lang\XPClass;
use lang\Type;
use lang\ArrayType;
use lang\MapType;
use lang\FunctionType;

class FromHHVMCode extends FromCode {

  static function __static() { }

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name) {
    if (isset($this->decl['method']['__construct'])) {
      foreach ($this->decl['method']['__construct']['params'] as $param) {
        if ($name === $param['name'] && $param['this']) return true;
      }
    }

    return parent::hasField($name);
  }

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name) {
    if (isset($this->decl['method']['__construct'])) {
      foreach ($this->decl['method']['__construct']['params'] as $param) {
        if ($name === $param['name'] && $param['this']) return $this->field($this->decl['name'], [
          'name'        => $name,
          'type'        => $param['type'],
          'access'      => $param['this'],
          'annotations' => [null => []],
          'comment'     => null
        ]);
      }
    }

    return parent::fieldNamed($name);
  }
}