<?php

class TranslationSorter {
    // Protected variables to use throughout the class
    private $originalFilePath;
    private $sortedFilePath;
    private $translations;

    public function __construct($originalFilePath, $sortedFilePath) {
        // Define the paths to the original and sorted translation files
        $this->originalFilePath = $originalFilePath;
        $this->sortedFilePath = $sortedFilePath;

        $this->translations = []; // Initialize an array to store translations
    }

    public function loadTranslations() {
        echo 'Reading the file: ' . $this->originalFilePath . '<br>';
        $lines = file($this->originalFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read the original translation file line by line

        $currentComment = '';

        // Looping through each line in the original file
        foreach ($lines as $line) {
            // Check if the line is a comment (starts with '#')
            if (strpos($line, '#') === 0) {
                $currentComment = $line; // Set the current comment
            }
            // Check if the line is a translation (contains '=')
            elseif (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2); // Split the line into key and value

                // Add the current translation to the list of array
                $this->translations[] = [
                    'comment' => $currentComment,
                    'key' => trim($key), // Trim will remove leading/trailing spaces
                    'value' => trim($value), // Trim will remove leading/trailing spaces
                ];

                $currentComment = '';
            }
        }
    }

    // Callback function for usort
    function sortCallBack($a, $b) {
        /*
         * Comparing $a['key'] with $b['key']
         * Returns 0 if equal value
         * Returns negative value if $a['key'] is less than $b['key']
         * Returns positive value if $a['key'] is greater than $b['key']
        */
        return strcmp($a['key'], $b['key']);

        //if ($a['key'] == $b['key']) return 0;
        //return ($a['key'] < $b['key']) ? -1 : 1;
    }

    // Sort translations alphabetically by key
    public function sortTranslations() {
        usort($this->translations, array($this, 'sortCallBack'));
    }

    // Create a new sorted translation file
    public function createSortedFile() {
        // Display a message if no translations found
        if (!empty($this->translations)) {
            $sortedContent = '';
            foreach ($this->translations as $translation) {
                if (!empty($translation['comment'])) {
                    $sortedContent .= $translation['comment'] . PHP_EOL; // Each translation's comment (if present) is added before key=value
                }
                $sortedContent .= $translation['key'] . '=' . $translation['value'] . PHP_EOL; // The sorted translation key and value is added
            }

            // Write the sorted content to the new file
            file_put_contents($this->sortedFilePath, $sortedContent);

            echo '<br>' . 'The sorted translation file "' . $this->sortedFilePath . '" is created successfully!';
        } else {
            echo '<br>' . 'No translations found!';
        }
    }

    public function processTranslations() {
        // Check if the original translation file exists
        if (file_exists($this->originalFilePath)) {
            $this->loadTranslations();
            $this->sortTranslations();
            $this->createSortedFile();
        } else {
            echo 'Original translation file not found.';
        }
    }
}

/*
 *
 * Usage
 *
*/

// Read the original translation file assuming that it's on the root directory of the server
$originalFilePath = $_SERVER['DOCUMENT_ROOT'] . '/orig-translation.properties';

// Path to newly created sorted translation file
$sortedFilePath = $_SERVER['DOCUMENT_ROOT'] . '/sorted-translation-' . strtotime(date('Y-m-d h:i:s')) . '.properties';;

// Calling the class and passing file paths
$translator = new TranslationSorter($originalFilePath, $sortedFilePath);
$translator->processTranslations();

?>