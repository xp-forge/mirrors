<?php namespace lang\mirrors;

use lang\XPClass;
use lang\Type;
use lang\ArrayType;
use lang\MapType;
use lang\FunctionType;

class FromHHVMCode extends FromCode {

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

  protected function type($name) {
    if (null === $name) {
      return null;
    } else if ('this' === $name) {
      return function() { return new XPClass($this->resolve0('self')); };
    } else if ('void' === $name) {
      return function() { return Type::$VOID; };
    } else if ('mixed' === $name) {
      return function() { return Type::$VAR; };
    } else if (0 === substr_compare($name, '[]', -2)) {
      return function() use($name) {
        $component= $this->type(substr($name, 0, -2));
        return new ArrayType($component());
      };
    } else if (0 === substr_compare($name, '[:', 0, 2)) {
      return function() use($name) {
        $component= $this->type(substr($name, 2, -1));
        return new MapType($component());
      };
    } else if (0 === substr_compare($name, 'function(', 0, 9)) {
      return function() use($name) { return FunctionType::forName($name); };
    } else {
      return parent::type($name);
    }
  }
}