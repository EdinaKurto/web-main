<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/rest/dao/ExamDao.php';

try {
    $dao = new ExamDao();
    echo "DAO initialized and connection successful!<br>";

    // call the helper method
    $tables = $dao->showTables();

    echo "<pre>";
    print_r($tables);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
