<?php
// Get admin credentials from environment variables
$admin_user = getenv('SMARTRIQS_ADMIN_USER') ?: 'admin';
$admin_password = getenv('SMARTRIQS_ADMIN_PASSWORD');

// Check if admin password is set in environment
if (empty($admin_password)) {
    die('Error: SMARTRIQS_ADMIN_PASSWORD environment variable not set');
}

// Basic authentication using environment variables
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $admin_user || $_SERVER['PHP_AUTH_PW'] !== $admin_password) {
    header('WWW-Authenticate: Basic realm="Admin Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required';
    exit;
}

$data_base_path = getenv('SMARTRIQS_DATA_BASE_PATH') ?: 'data';
$baseDir = $data_base_path . '/';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Replace deprecated FILTER_SANITIZE_STRING with htmlspecialchars
    $researcherId = htmlspecialchars(trim($_POST['researcher_id'] ?? ''), ENT_QUOTES, 'UTF-8');
    $studyId = htmlspecialchars(trim($_POST['study_id'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    if ($researcherId && preg_match('/^[a-zA-Z0-9_]+$/', $researcherId)) {
        // Create researcher directory
        $researcherDir = $baseDir . $researcherId;
        if (!file_exists($researcherDir)) {
            mkdir($researcherDir, 0755, true);
            
            // If study ID provided, create study structure
            if ($studyId && preg_match('/^[a-zA-Z0-9_]+$/', $studyId)) {
                // Create chat logs directory
                $chatLogsDir = $researcherDir . '/' . $studyId . '_chat_logs/Group_1';
                mkdir($chatLogsDir, 0755, true);
                
                // Create empty data file with header
                $dataFile = $researcherDir . '/' . $studyId . '_rawdata.csv';
                $header = 'Group ID,Condition,Group status,A,Last active,A#1,B,Last active,B#1';
                file_put_contents($dataFile, $header);
                
                $message = "Successfully created researcher '$researcherId' with study '$studyId'";
            } else {
                $message = "Successfully created researcher '$researcherId'";
            }
            
            // Set permissions
            exec("chown -R www-data:www-data " . escapeshellarg($researcherDir));
            exec("chmod -R 755 " . escapeshellarg($researcherDir));
        } else {
            $message = "Researcher ID '$researcherId' already exists";
        }
    } else {
        $message = "Invalid researcher ID. Use only letters, numbers, and underscores.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SMARTRIQS Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .message { margin: 15px 0; padding: 10px; background: #f8f8f8; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMARTRIQS Admin Panel</h1>
        
        <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="researcher_id">Researcher ID:</label>
                <input type="text" id="researcher_id" name="researcher_id" required>
            </div>
            
            <div class="form-group">
                <label for="study_id">Study ID (optional):</label>
                <input type="text" id="study_id" name="study_id">
            </div>
            
            <button type="submit">Create</button>
        </form>
    </div>
</body>
</html>
