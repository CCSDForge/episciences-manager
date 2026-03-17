<?php

use Psr\Log\LoggerInterface;

it('can create a page controller instance', function () {
    $logger = $this->createMock(LoggerInterface::class);
    $controller = new \App\Controller\PageController($logger);

    expect($controller)->toBeInstanceOf(\App\Controller\PageController::class);
});

it('has a valid namespace', function () {
    $class = new ReflectionClass(\App\Controller\PageController::class);
    
    expect($class->getNamespaceName())->toBe('App\Controller');
});