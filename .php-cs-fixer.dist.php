<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setFinder(
        Finder::create()
            ->in(__DIR__ . '/src')
            ->append([
                __FILE__,
            ]),
    )
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__ . '/var/' . basename(__FILE__, '.dist.php') . '.cache')
    ->setRules([
        '@PER-CS2.0' => true,
        'final_class' => true,
    ]);
