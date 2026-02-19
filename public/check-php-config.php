<?php
// Simple script to check PHP upload configuration
// Access this at: http://localhost/capstone-thomas/public/check-php-config.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Upload Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .warning { color: #ff6600; font-weight: bold; }
        .good { color: #00aa00; font-weight: bold; }
        .bad { color: #cc0000; font-weight: bold; }
    </style>
</head>
<body>
    <h1>PHP Upload Configuration Check</h1>
    <p>This page shows your current PHP upload limits.</p>
    
    <table>
        <tr>
            <th>Setting</th>
            <th>Current Value</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>upload_max_filesize</td>
            <td><?php echo ini_get('upload_max_filesize'); ?></td>
            <td class="<?php 
                $upload_max = ini_get('upload_max_filesize');
                $upload_max_bytes = return_bytes($upload_max);
                echo ($upload_max_bytes >= 50 * 1024 * 1024) ? 'good">✓ OK' : 'bad">✗ Too small (need 50M)'; 
            ?>"></td>
        </tr>
        <tr>
            <td>post_max_size</td>
            <td><?php echo ini_get('post_max_size'); ?></td>
            <td class="<?php 
                $post_max = ini_get('post_max_size');
                $post_max_bytes = return_bytes($post_max);
                echo ($post_max_bytes >= 50 * 1024 * 1024) ? 'good">✓ OK' : 'bad">✗ Too small (need 50M)'; 
            ?>"></td>
        </tr>
        <tr>
            <td>max_execution_time</td>
            <td><?php echo ini_get('max_execution_time'); ?> seconds</td>
            <td class="<?php 
                $max_exec = ini_get('max_execution_time');
                echo ($max_exec == 0 || $max_exec >= 300) ? 'good">✓ OK' : 'warning">⚠ Should be 300+'; 
            ?>"></td>
        </tr>
        <tr>
            <td>max_input_time</td>
            <td><?php echo ini_get('max_input_time'); ?> seconds</td>
            <td class="<?php 
                $max_input = ini_get('max_input_time');
                echo ($max_input == -1 || $max_input >= 300) ? 'good">✓ OK' : 'warning">⚠ Should be 300+'; 
            ?>"></td>
        </tr>
        <tr>
            <td>memory_limit</td>
            <td><?php echo ini_get('memory_limit'); ?></td>
            <td class="<?php 
                $memory_limit = ini_get('memory_limit');
                $memory_bytes = return_bytes($memory_limit);
                echo ($memory_bytes >= 128 * 1024 * 1024) ? 'good">✓ OK' : 'warning">⚠ Low'; 
            ?>"></td>
        </tr>
        <tr>
            <td>file_uploads</td>
            <td><?php echo ini_get('file_uploads') ? 'On' : 'Off'; ?></td>
            <td class="<?php echo ini_get('file_uploads') ? 'good">✓ Enabled' : 'bad">✗ Disabled'; ?>"></td>
        </tr>
        <tr>
            <td>max_file_uploads</td>
            <td><?php echo ini_get('max_file_uploads'); ?></td>
            <td class="good">✓ OK</td>
        </tr>
    </table>
    
    <h2>Recommendations:</h2>
    <?php
    $issues = [];
    
    if ($upload_max_bytes < 50 * 1024 * 1024) {
        $issues[] = "upload_max_filesize is too small. Need at least 50M, currently: " . ini_get('upload_max_filesize');
    }
    
    if ($post_max_bytes < 50 * 1024 * 1024) {
        $issues[] = "post_max_size is too small. Need at least 50M, currently: " . ini_get('post_max_size');
    }
    
    if ($max_exec > 0 && $max_exec < 300) {
        $issues[] = "max_execution_time is too low. Should be at least 300 seconds, currently: " . $max_exec;
    }
    
    if (!ini_get('file_uploads')) {
        $issues[] = "file_uploads is disabled!";
    }
    
    if (count($issues) > 0) {
        echo '<div style="background: #ffeeee; padding: 15px; border: 2px solid #cc0000; border-radius: 5px;">';
        echo '<h3 class="bad">Issues Found:</h3><ul>';
        foreach ($issues as $issue) {
            echo '<li>' . htmlspecialchars($issue) . '</li>';
        }
        echo '</ul>';
        echo '<p><strong>To fix these issues, you need to edit your php.ini file:</strong></p>';
        echo '<ol>';
        echo '<li>Open XAMPP Control Panel</li>';
        echo '<li>Click "Config" button next to Apache</li>';
        echo '<li>Select "PHP (php.ini)"</li>';
        echo '<li>Search for each setting above and change the values</li>';
        echo '<li>Save the file and restart Apache</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div style="background: #eeffee; padding: 15px; border: 2px solid #00aa00; border-radius: 5px;">';
        echo '<p class="good">✓ All settings look good! Your PHP configuration should support uploading files up to 50MB.</p>';
        echo '</div>';
    }
    ?>
    
    <h2>PHP Info:</h2>
    <p><a href="phpinfo.php" target="_blank">View full phpinfo()</a> (if phpinfo.php exists)</p>
    
    <p style="margin-top: 40px; color: #666; font-size: 12px;">
        Generated: <?php echo date('Y-m-d H:i:s'); ?><br>
        PHP Version: <?php echo phpversion(); ?><br>
        Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
    </p>
</body>
</html>

<?php
// Helper function to convert PHP ini values to bytes
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    
    return $val;
}
?>
