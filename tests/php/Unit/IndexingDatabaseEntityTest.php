<?php

use App\Entity\IndexingDatabase;
use App\Entity\Review;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;

// ============================================
// Constructor defaults
// ============================================

it('initializes with PENDING status by default', function () {
    $database = new IndexingDatabase();
    expect($database->getStatus())->toBe(IndexingDatabaseStatus::PENDING);
});

it('initializes with createdAt set to current time', function () {
    $before = new \DateTime();
    $database = new IndexingDatabase();
    $after = new \DateTime();

    expect($database->getCreatedAt())->toBeInstanceOf(\DateTimeInterface::class);
    expect($database->getCreatedAt() >= $before)->toBeTrue();
    expect($database->getCreatedAt() <= $after)->toBeTrue();
});

it('initializes with updatedAt set to current time', function () {
    $before = new \DateTime();
    $database = new IndexingDatabase();
    $after = new \DateTime();

    expect($database->getUpdatedAt())->toBeInstanceOf(\DateTimeInterface::class);
    expect($database->getUpdatedAt() >= $before)->toBeTrue();
    expect($database->getUpdatedAt() <= $after)->toBeTrue();
});

it('initializes with empty reviews collection', function () {
    $database = new IndexingDatabase();
    expect($database->getReviews())->toBeEmpty();
});

// ============================================
// setStatus updates updatedAt
// ============================================

it('updates updatedAt when status changes', function () {
    $database = new IndexingDatabase();
    $originalUpdatedAt = $database->getUpdatedAt();

    // Small delay to ensure time difference
    usleep(1000);

    $database->setStatus(IndexingDatabaseStatus::VALIDATED);

    expect($database->getUpdatedAt())->toBeGreaterThanOrEqual($originalUpdatedAt);
    expect($database->getStatus())->toBe(IndexingDatabaseStatus::VALIDATED);
});

// ============================================
// isValidated method
// ============================================

it('returns true for isValidated when status is VALIDATED', function () {
    $database = new IndexingDatabase();
    $database->setStatus(IndexingDatabaseStatus::VALIDATED);

    expect($database->isValidated())->toBeTrue();
});

it('returns false for isValidated when status is PENDING', function () {
    $database = new IndexingDatabase();
    expect($database->isValidated())->toBeFalse();
});

it('returns false for isValidated when status is REJECTED', function () {
    $database = new IndexingDatabase();
    $database->setStatus(IndexingDatabaseStatus::REJECTED);

    expect($database->isValidated())->toBeFalse();
});

// ============================================
// Review collection management
// ============================================

it('can add a review', function () {
    $database = new IndexingDatabase();
    $review = new Review();

    $database->addReview($review);

    expect($database->getReviews())->toHaveCount(1);
    expect($database->getReviews()->contains($review))->toBeTrue();
});

it('does not add duplicate reviews', function () {
    $database = new IndexingDatabase();
    $review = new Review();

    $database->addReview($review);
    $database->addReview($review);

    expect($database->getReviews())->toHaveCount(1);
});

it('can remove a review', function () {
    $database = new IndexingDatabase();
    $review = new Review();

    $database->addReview($review);
    $database->removeReview($review);

    expect($database->getReviews())->toBeEmpty();
});

// ============================================
// Basic setters/getters
// ============================================

it('can set and get name', function () {
    $database = new IndexingDatabase();
    $database->setName('Scopus');

    expect($database->getName())->toBe('Scopus');
});

it('can set and get url', function () {
    $database = new IndexingDatabase();
    $database->setUrl('https://www.scopus.com');

    expect($database->getUrl())->toBe('https://www.scopus.com');
});

it('can set and get logo', function () {
    $database = new IndexingDatabase();
    $database->setLogo('data/indexing-databases/scopus.png');

    expect($database->getLogo())->toBe('data/indexing-databases/scopus.png');
});

it('can set and get createdBy', function () {
    $database = new IndexingDatabase();
    $user = new User();
    $user->setUsername('admin');

    $database->setCreatedBy($user);

    expect($database->getCreatedBy())->toBe($user);
    expect($database->getCreatedBy()->getUsername())->toBe('admin');
});