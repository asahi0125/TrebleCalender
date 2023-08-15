<?php
// データベース接続
function connectDB() {
    try {
        $pdo = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
        return $pdo;

    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}

function getSchedulesByDate($pdo, $date) {
    $sql = 'SELECT * FROM schedules WHERE CAST(start_datetime AS DATE) = :start_datetime ORDER BY start_datetime ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_datetime', $date, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll();
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}
?>

