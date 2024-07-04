<?php

$finder = new TwigCsFixer\File\Finder();
$finder->in('Resources/views');
// Exclude our (almost) verbatim copy of template.
$finder->notPath('app/timesheet/edit-default.html.twig');

$config = new TwigCsFixer\Config\Config();
$config->setFinder($finder);

return $config;
