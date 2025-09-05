<?php

require_once 'vendor/autoload.php';

use App\Service\MarkdownService;

// Test with figure containing figcaption
$markdownService = new MarkdownService();

// Test 1: Figure with image and figcaption (CKEditor format)
$htmlWithFigcaption = '<figure class="image image_resized" style="width:50%;"><img src="http://example.com/image.jpg" alt="Test image" style="aspect-ratio:1815/1302;" width="1815" height="1302"><figcaption>This is the image description</figcaption></figure>';

echo "=== TEST FIGCAPTION PRESERVATION ===\n";
echo "Original HTML with figcaption:\n" . $htmlWithFigcaption . "\n\n";

$markdown = $markdownService->toMarkdown($htmlWithFigcaption);
echo "Converted to Markdown:\n" . $markdown . "\n\n";

$htmlBack = $markdownService->toHtml($markdown);
echo "Converted back to HTML:\n" . $htmlBack . "\n\n";

// Test comparison
echo "=== COMPARISON ===\n";
echo "BEFORE: " . (strpos($htmlWithFigcaption, '<figcaption>') !== false ? '✅ Has figcaption' : '❌ No figcaption') . "\n";
echo "AFTER:  " . (strpos($htmlBack, '<figcaption>') !== false ? '✅ Has figcaption' : '❌ No figcaption') . "\n";
echo "BEFORE: " . (strpos($htmlWithFigcaption, 'This is the image description') !== false ? '✅ Has description text' : '❌ No description') . "\n";
echo "AFTER:  " . (strpos($htmlBack, 'This is the image description') !== false ? '✅ Has description text' : '❌ No description') . "\n";

// Test 2: Simple figure without figcaption
echo "\n=== TEST WITHOUT FIGCAPTION ===\n";
$htmlWithoutFigcaption = '<figure class="image image_resized" style="width:50%;"><img src="http://example.com/image.jpg" alt="Test image" style="aspect-ratio:1815/1302;" width="1815" height="1302"></figure>';

echo "Original HTML without figcaption:\n" . $htmlWithoutFigcaption . "\n\n";

$markdown2 = $markdownService->toMarkdown($htmlWithoutFigcaption);
echo "Converted to Markdown:\n" . $markdown2 . "\n\n";

$htmlBack2 = $markdownService->toHtml($markdown2);
echo "Converted back to HTML:\n" . $htmlBack2 . "\n\n";