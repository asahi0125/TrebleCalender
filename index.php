<?php
require_once('config.php');
require_once('functions.php');

$title = SITE_NAME;


if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {

    $ym = date('Y-m');
}

$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

$day_count = date('t', $timestamp);

$youbi = date('w', $timestamp);

$html_title = date('Y年n月', $timestamp);

$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

$today = date('Y-m-d');

$weeks = [];
$week = '';

$week .= str_repeat('<td></td>', $youbi);

$pdo = connectDB();

for ( $day = 1; $day <= $day_count; $day++, $youbi++){

    $date = $ym . '-' . sprintf('%02d', $day);

    // 予定を取得
    $sql = 'SELECT * FROM schedules WHERE CAST(start_datetime AS DATE) = :start_datetime ORDER BY start_datetime ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_datetime', $date, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // HTML作成
    if ($date == $today) {
        $week .= '<td class="today">';
    } else {
        $week .= '<td>';
    }

    $week .= '<a href="detail.php?ymd=' . $date . '">' . $day;

    if (!empty($rows)) {
        $week .= '<div class="badges">';
            foreach ($rows as $row) {
                $task = date('H:i', strtotime($row['start_datetime'])) . ' ' . h($row['task']);
                $week .= '<span class="badge text-wrap ' . $row['color'] . '">' . $task . '</span>';
            }
        $week .= '</div>';
    }

    $week .= '</a></td>';

    // 日曜日、または、最終日の場合
    if ($youbi % 7 == 6 || $day == $day_count) {
        

        if ($day == $day_count) {
            // 月の最終日の場合、空セルを追加
            // 例）最終日が金曜日の場合、土・日曜日の空セルを追加
            $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
        }

        // weeks配列にtrと$weekを追加する
        $weeks[] = '<tr>' . $week . '</tr>';

        // weekをリセット
        $week = '';
     }
}
?>
<!DOCTYPE html>
<html lang="ja" class="h-100">
<head>
    <?php require_once('elements/head.php'); ?>
</head>
<body class="d-flex flex-column h-100">
<header>
   <?php require_once('elements/navbar.php'); ?>
</header>
<main>
    <div class="container">
        <table class="table table-bordered calendar">
            <thead>
                <tr class="head-cal fs-4">
                    <th colspan="1" class="text-start"><a href="index.php?ym=<?= $prev; ?>">&lt;</a></th>
                    <th colspan="5"><?= $html_title; ?></th>
                    <th colspan="1" class="text-end"><a href="index.php?ym=<?= $next; ?>">&gt;</a></th>
                </tr>
                <tr class="head-week">
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                    <
                </tr>
            </thead>
            <tbody>
              
                <?php foreach ($weeks as $week) { echo $week; }?>
            </tbody>
        </table>
    </div>
</main>
    <?php require_once('elements/footer.php'); ?>
</body>
</html>


