<?php
require 'vendor/autoload.php';

use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new GithubFlavoredMarkdownConverter([
    'html_input' => 'allow',
    'allow_unsafe_links' => false,
]);

$markdown = "|              |                     |                                                
  | ------------ | ------------------- |                                                             
  | hhhhhhhhhhhh | iiiiiiiiiiiiiiiiiii |                                                             
  | jjjjjjjjjj   | kkkkkkkkkkkkkkkkk   |";

echo $converter->convert($markdown)->getContent();
