<?php

require_once 'vendor/autoload.php';

use App\Service\MarkdownService;

// Test HTML->Markdown->HTML conversion with images
$markdownService = new MarkdownService();

// Test 1: Simple image via insertImageViaUrl
$htmlTest1 = '<p>Here is an image:</p><figure class="image"><img src="http://epimanager.episciences.org/en/epijinfo/resources/Capture-d-ecran-du-2025-08-25-14-53-58.png" alt="Test image"></figure><p>Content after image</p>';

echo "=== TEST 1: Image with figure ===\n";
echo "Original HTML:\n" . $htmlTest1 . "\n\n";

$markdown1 = $markdownService->toMarkdown($htmlTest1);
echo "Converted to Markdown:\n" . $markdown1 . "\n\n";

$htmlBack1 = $markdownService->toHtml($markdown1);
echo "Converted back to HTML:\n" . $htmlBack1 . "\n\n";

// Test 2: Simple image without figure
$htmlTest2 = '<p>Here is an image:</p><img src="http://epimanager.episciences.org/en/epijinfo/resources/Capture-d-ecran-du-2025-08-25-14-53-58.png" alt="Test image"><p>Content after image</p>';

echo "=== TEST 2: Simple image ===\n";
echo "Original HTML:\n" . $htmlTest2 . "\n\n";

$markdown2 = $markdownService->toMarkdown($htmlTest2);
echo "Converted to Markdown:\n" . $markdown2 . "\n\n";

$htmlBack2 = $markdownService->toHtml($markdown2);
echo "Converted back to HTML:\n" . $htmlBack2 . "\n\n";

// Test 3: HTML as produced by CKEditor
$htmlTest3 = '<figure class="image"><img style="aspect-ratio:2473/1294;" src="http://epimanager.episciences.org/en/epijinfo/resources/Capture-d-ecran-du-2025-08-25-14-53-58.png" width="2473" height="1294"></figure><p>Test content after image</p>';

echo "=== TEST 3: CKEditor style HTML ===\n";
echo "Original HTML:\n" . $htmlTest3 . "\n\n";

$markdown3 = $markdownService->toMarkdown($htmlTest3);
echo "Converted to Markdown:\n" . $markdown3 . "\n\n";

$htmlBack3 = $markdownService->toHtml($markdown3);
echo "Converted back to HTML:\n" . $htmlBack3 . "\n\n";