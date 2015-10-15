#!/usr/bin/env php
<?php
$file = $argv[1];
$projectdir = $argv[2];

preg_match('#^(.*/)([^\/]+)\.php$#', $file, $matches);
$classname = $matches[2];
$filedir = $matches[1];

$namespace = lookup_namespace_for_filedir(
  $filedir,
  get_psr4_dir_to_namespace_mapping(
    get_composer_config($projectdir)));

print_header($namespace, $classname);

function lookup_namespace_for_filedir($filedir, $namespace_mapping)
{
  if (!$namespace_mapping)
    return;
  foreach ($namespace_mapping as $dir => $ns)
  {
    preg_match("#^($dir)(.*?)$#", $filedir, $matches);
    if (!$matches[1])
      continue;
    $subns = $matches[2];
    if ($subns)
      $ns .= preg_replace("#/#", "\\", $subns);
    $ns = preg_replace("/\\\\$/", "", $ns);
    return $ns;
  }
  return null;
}

function get_psr4_dir_to_namespace_mapping($composer_config)
{
  $dirs = array();
  if (empty($composer_config))
    return;
  if (!isset($composer_config['autoload']['psr-4']))
    return;
  foreach ($composer_config['autoload']['psr-4'] as $ns => $dir)
  {
    $dirs[$dir] = $ns;
  }
  return $dirs;
}

function get_composer_config($projectdir)
{
  $filename = "$projectdir/composer.json";
  if (!file_exists($filename))
    return;
  return json_decode(file_get_contents($filename), true);
}

function print_header($namespace, $classname)
{
  if ($namespace)
    echo "<?php namespace $namespace;\n";
  else
    echo "<?php\n";
  echo <<<EOF

class $classname
{
}
EOF;
}

