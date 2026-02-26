<?php
$dir = "c:/dev/RutiTruck/Api_Ruti/frontend";
$files = glob("$dir/*.php");
foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = preg_replace('/\b(bg|text|border|ring|focus:ring|focus:border)-blue-(\d+)\b/', '$1-red-$2', $content);
        
        // Custom paginator color replacement in admin styles
        $content = str_replace('#dc2626', '#dc2626', $content); // Tailwind blue-600 to red-600
        
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
echo "All files updated.\n";
