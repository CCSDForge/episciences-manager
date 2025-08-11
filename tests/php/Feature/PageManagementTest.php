<?php

it('can validate page controller exists', function () {
    expect(class_exists('App\Controller\PageController'))->toBeTrue();
});

it('can validate review controller exists', function () {
    expect(class_exists('App\Controller\ReviewController'))->toBeTrue();
});

it('can validate user controller exists', function () {
    expect(class_exists('App\Controller\UserController'))->toBeTrue();
});

it('can validate default controller exists', function () {
    expect(class_exists('App\Controller\DefaultController'))->toBeTrue();
});