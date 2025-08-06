<?php

it('can create a page controller instance', function () {
    $controller = new \App\Controller\PageController();
    
    expect($controller)->toBeInstanceOf(\App\Controller\PageController::class);
});

it('has a valid namespace', function () {
    $class = new ReflectionClass(\App\Controller\PageController::class);
    
    expect($class->getNamespaceName())->toBe('App\Controller');
});