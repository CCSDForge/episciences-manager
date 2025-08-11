<?php

it('can validate review entity exists', function () {
    expect(class_exists('App\Entity\Review'))->toBeTrue();
});

it('can validate review repository exists', function () {
    expect(class_exists('App\Repository\ReviewRepository'))->toBeTrue();
});

it('can validate review manager service exists', function () {
    expect(class_exists('App\Service\ReviewManager'))->toBeTrue();
});

it('can create review controller instance', function () {
    $controller = new \App\Controller\ReviewController();
    expect($controller)->toBeInstanceOf(\App\Controller\ReviewController::class);
});