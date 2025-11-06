<?php

require_once 'core/config.php';

$databaseDir = __DIR__ . '/account';


$migrationsTable = "CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($migrationsTable)) {
    die("❌ Failed to create migrations table: " . $conn->error);
}


$executed = [];
$result = $conn->query("SELECT filename FROM migrations");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $executed[] = $row['filename'];
    }
    $result->free();
}


$sqlFiles = glob($databaseDir . '/*.sql');
if (empty($sqlFiles)) {
    die("⚠️ No .sql files found in {$databaseDir}");
}

foreach ($sqlFiles as $file) {
    $filename = basename($file);


    if (in_array($filename, $executed)) {
        echo "⏭️ Skipped: {$filename} (already executed)<br>";
        continue;
    }

    echo "📄 Executing: {$filename} ... ";

    $sql = file_get_contents($file);
    if ($sql === false) {
        echo "❌ Failed to read file.<br>";
        continue;
    }

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());

        // record migration
        $stmt = $conn->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->close();

        echo "✅ Success<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

$conn->close();
