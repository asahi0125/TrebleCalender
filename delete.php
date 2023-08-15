<?php
require_once('config.php');
require_once('functions.php');


if (!isset($_GET['ymd']) || strtotime($_GET['ymd']) === false) {

    header('Location:index.php');
    exit();
}
$ymd = $_GET['ymd'];

$ymd_formatted = date('Y年n月j日', strtotime($ymd));
$title = $ymd_formatted . 'の予定 | ' . SITE_NAME;

$pdo = connectDB();
$rows = getSchedulesByDate($pdo, $ymd);

// echo '<pre>';
// echo var_dump($rows);
// echo '</pre>';
?>
<!DOCTYPE html>
<html lang="ja" class="h-100">
<head>
    <?php require_once('elements/head.php'); ?>
</head>
<body class="d-flex flex-column h-100">
    <?php require_once('elements/navbar.php'); ?>
<main>
    <div class="container">
    <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <h4 class="text-center"><?= $ymd_formatted; ?></h4>
                <?php if (!empty($rows)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 3%;"></th>
                            <th style="width: 25%;"><i class="fa-regular fa-clock"></i></th>
                            <th style="width: 50%;"><i class="fa-solid fa-list"></i></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td><i class="fa-solid fa-square text-primary"></i></td>
                            <td>10:00 ~ 11:25</td>
                            <td>ここに予定を表示します。</td>
                            <td>
                                <a href="edit.php" class="btn btn-sm btn-link">編集</a>
                                <a href="#" class="btn btn-sm btn-link">削除</a>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-square text-danger"></i></td>
                            <td>12:45 ~ 6/17 11:45</td>
                            <td>ここに予定を表示します。</td>
                            <td>
                                <a href="edit.php" class="btn btn-sm btn-link">編集</a>
                                <a href="#" class="btn btn-sm btn-link">削除</a>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-square text-warning"></i></td>
                            <td>19:00 ~ 20:05</td>
                            <td>ここに予定を表示します。</td>
                            <td>
                                <a href="edit.php" class="btn btn-sm btn-link">編集</a>
                                <a href="#" class="btn btn-sm btn-link">削除</a>
                            </td>
                        </tr>
                    </tbody> -->
                    <?php foreach ($rows as $row): ?>
                                <?php
                                    $color = str_replace('bg', 'text', $row['color']);
                                    $start = date('H:i', strtotime($row['start_datetime']));

                                    $start_date = date('Y-m-d', strtotime($row['start_datetime']));
                                    $end_date = date('Y-m-d', strtotime($row['end_datetime']));

                                    if ($start_date == $end_date) {
                                        $end = date('H:i', strtotime($row['end_datetime']));
                                    } else {
                                        $end = date('n/j H:i', strtotime($row['end_datetime']));
                                    }
                                ?>
                                <tr>
                                    <td><i class="fas fa-square <?= $color; ?>"></i></td>
                                    <td><?= $start; ?> ~ <?= $end; ?></td>
                                    <td><?= $row['task']; ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['schedule_id']; ?>" class="btn btn-sm btn-link">編集</a>
                                        <a href="javascript:void(0);"
                                        onclick="var ok=confirm('この予定を削除してもよろしいですか？'); if(ok) location.href='delete.php?id=<?= $row['schedule_id']; ?>'"
                                        class="btn btn-sm btn-link">削除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                </table>
                <?php else: ?>
                    <div class="alert alert-dark" role="alert">
                        予定がありません。予定の追加は<a href="add.php" class="alert-link">こちら</a>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</main>
    <?php require_once('elements/footer.php'); ?>
</body>
</html>

