<?php

require_once 'vendor/autoload.php';

use App\Service\MarkdownService;

// Test with exact CKEditor HTML (resized to 25%)
$markdownService = new MarkdownService();

$ckEditorHtml = '<p><img class="image_resized" style="aspect-ratio:1815/1302;width:25%;" src="http://epimanager-dev.episciences.org/en/epijinfo/resources/Capture-d-ecran-du-2025-08-25-14-37-09.png" alt="Capture-d-ecran-du-2025-08-25-14-37-09.png" width="1815" height="1302"></p>';

echo "=== TEST CKEDITOR RESIZED IMAGE ===\n";
echo "Original CKEditor HTML:\n" . $ckEditorHtml . "\n\n";

$markdown = $markdownService->toMarkdown($ckEditorHtml);
echo "Converted to Markdown:\n" . $markdown . "\n\n";

$htmlBack = $markdownService->toHtml($markdown);
echo "Converted back to HTML:\n" . $htmlBack . "\n\n";

// Test comparison: before vs after
echo "=== COMPARISON ===\n";
echo "BEFORE: " . (strpos($ckEditorHtml, 'width:25%') !== false ? '✅ Has 25% width' : '❌ No width') . "\n";
echo "AFTER:  " . (strpos($htmlBack, 'width:25%') !== false ? '✅ Has 25% width' : '❌ No width') . "\n";
echo "BEFORE: " . (strpos($ckEditorHtml, 'image_resized') !== false ? '✅ Has image_resized class' : '❌ No class') . "\n";
echo "AFTER:  " . (strpos($htmlBack, 'image_resized') !== false ? '✅ Has image_resized class' : '❌ No class') . "\n";