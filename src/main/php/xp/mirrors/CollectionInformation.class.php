<?php namespace xp\mirrors;

use lang\mirrors\TypeMirror;

abstract class CollectionInformation extends Information {

  protected function displayCollection($package, $loader, $out) {
    $ext= strlen(\xp::CLASS_FILE_EXT);
    $order= [
      'interface' => [],
      'trait'     => [],
      'enum'      => [],
      'class'     => []
    ];

    // Child packages
    foreach ($loader->packageContents($package->name()) as $entry) {
      $base= $package->isGlobal() ? '' : $package->name().'.';
      if ('/' === $entry{strlen($entry) - 1}) {
        $out->writeLine('  package ', $base.substr($entry, 0, -1));
      } else if (0 === substr_compare($entry, \xp::CLASS_FILE_EXT, -$ext)) {
        $mirror= new TypeMirror($loader->loadClass($base.substr($entry, 0, -$ext)));
        $order[$mirror->kind()->name()][]= self::declarationOf($mirror);
      }
    }

    // Types    
    foreach ($order as $type => $types) {
      if (empty($types)) continue;

      $out->writeLine();
      sort($types);
      foreach ($types as $name) {
        $out->writeLine('  ', $name);
      }
    }
  }
}