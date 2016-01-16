<?php namespace xp\mirrors;

use util\cmd\Console;
use lang\ClassLoader;
use lang\mirrors\Package;

/**
 * Displays information about types or packages
 * ====================================================================
 *
 * - Show information about a type
 *   ```sh
 *   $ xp mirror lang.Value
 *   ```
 * - Show information about a file declaring a type
 *   ```sh
 *   $ xp mirror src/main/php/Example.class.php
 *   ```
 * - Show information about a package
 *   ```sh
 *   $ xp mirror lang.reflect
 *   ```
 * - Show information about a directory
 *   ```sh
 *   $ xp mirror src/test/php
 *   ```
 */
class MirrorRunner {

  /**
   * Main
   *
   * @param  string[] $args
   * @return int
   */
  public static function main($args) {
    $name= array_shift($args);
    if (null === $name) {
      Console::$err->writeLine('*** No class or package name given');
      return 1;
    }

    // Check whether a file, class or a package directory or name is given
    $cl= ClassLoader::getDefault();
    if (strstr($name, \xp::CLASS_FILE_EXT)) {
      $info= new TypeInformation($cl->loadUri(realpath($name)));
    } else if (is_dir($name)) {
      $info= new DirectoryInformation($name);
    } else if ($cl->providesClass($name)) {
      $info= new TypeInformation($cl->loadClass($name));
    } else if ($cl->providesPackage($name)) {
      $info= new PackageInformation(new Package($name));
    } else {
      Console::$err->writeLine('*** No classloader provides '.$name);
      return 2;
    }

    foreach ($info->sources() as $source) {
      Console::writeLine("\e[33m@", $source, "\e[0m");
    }
    $info->display(new Highlighting(Console::$out, [
      '/(class|enum|trait|interface|package|directory) (.+)/' => "\e[1;35m\$1\e[0m \$2",
      '/(extends|implements) (.+)/'                           => "\e[1;35m\$1\e[0m \$2",
      '/(public|private|protected|abstract|final|static)/'    => "\e[1;35m\$1\e[0m",
      '/(\$[a-zA-Z0-9_]+)/'                                   => "\e[1;31m\$1\e[0m",
    ]));
    return 0;
  }
}