<?php
require_once('config.php');
require_once('functions.php');

$title = '予定の追加 | ' . SITE_NAME;

// 存在・形式チェック
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location:index.php');
    exit();
}

$schedule_id = $_GET['id'];

$pdo = connectDB();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // 編集する予定データを取得する
    $sql = 'SELECT * FROM schedules WHERE schedule_id = :schedule_id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // データが見つからなかった場合
    if (empty($row) || $row === false) {
        header('Location:index.php');
        exit();
    }

    $start_datetime = $row['start_datetime'];
    $end_datetime = $row['end_datetime'];
    $task = $row['task'];
    $color = $row['color'];
    $err = [];

} else {
    // 予定を編集する
    $start_datetime = $_POST['start_datetime'];
    $end_datetime =  $_POST['end_datetime'];
    $task = $_POST['task'];
    $color = $_POST['color'];

    // 入力チェック
    if ($start_datetime == '') {
        $err['start_datetime'] = '開始日時を入力して下さい。';
    }

    if ($end_datetime == '') {
        $err['end_datetime'] = '終了日時を入力して下さい。';
    }

    if ($task == '') {
        $err['task'] = '予定を入力してください。';
    } else if (mb_strlen($task) > 128) {
        $err['task'] = '128文字以内で入力してください。';
    }

    if ($color == '') {
        $err['color'] = 'カラーを選択してください。';
    }

    // エラーが無ければデータベースに保存
    if (empty($err)) {
        $sql = 'UPDATE schedules 
                SET start_datetime = :start_datetime, end_datetime = :end_datetime, task = :task, color = :color, modified_at = now() 
                WHERE schedule_id = :schedule_id';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':task', $task, PDO::PARAM_STR);
        $stmt->bindValue(':color', $color, PDO::PARAM_STR);
        $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location:detail.php?ymd='.date('Y-m-d', strtotime($start_datetime)));
        exit();
    }
}
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
                <h4 class="text-center">予定の編集</h4>

                <form method="post">
                    <div class="mb-4 dp-parent">
                        <label for="inputStartDateTime" class="form-label">開始日時</label>
                        <input type="text" name="start_datetime" id="inpurStartDateTime" class="form-control task-datetime <?php if (!empty($err['start_datetime'])) echo 'is-invalid'; ?>" placeholder="開始日時を選択してください。" value="<?= $start_datetime; ?>">
                        <?php if (!empty($err['start_datetime'])): ?>
                        <div id="inputStartDateTimeFeeback" class="invalid-feeback">
                            *<?= $err['start_datetime']; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4 dp-parent">
                        <label for="inputEndDateTime" class="form-label">終了日時</label>
                        <input type="text" name="end_datetime" id="inputEndDateTime" class="form-control task-datetime <?php if (!empty($err['end_datetime'])) echo 'is-invalid'; ?>" placeholder="終了日時を選択して下さい" value="<?= $end_datetime; ?>">
                        <?php if (!empty($err['end_datetime'])): ?>
                        <div id="inputEndDateTimeFeedback" class="invalid-feedback">
                            * <?= $err['end_datetime']; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="inputTask" class="form-label">予定</label>
                        <input type="text" name="task" id="inputTask" class="form-control <?php if (!empty($err['task'])) echo 'is-invalid'; ?>" placeholder="予定を入力してください。" value="<?= $task; ?>">
                    <?php if (!empty($err['task'])): ?>
                        <div id="inputTaskFeedback" class="invalid-feedback">
                            * <?= $err['task']; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-5">
                        <label for="selectColor" class="form-label">カラー</label>
                        <select name="color" id="selectColor" class="form-select <?php if (!empty($err['color'])) echo 'is-invalid'; ?>">
                            <?php foreach(COLOR_LIST as $key => $val):?>
                                <option value="<?= $key; ?>" <?php if ($color == $key) echo 'selected'; ?>><?= $val; ?></option>
                            <?php endforeach; ?>
                            <!-- <option value="bg-light" selected>デフォルト</option>
                            <option value="bg-danger">赤</option>
                            <option value="bg-warning">オレンジ</option>
                            <option value="bg-primary">青</option>
                            <option value="bg-info">水色</option>
                            <option value="bg-success">緑</option>
                            <option value="bg-dark">黒</option>
                            <option value="bg-secondary">グレー</option> -->
                            
                        </select>
                                
                        <?php if (!empty($err['color'])): ?>
                            <div id="inputColorFeedback" class="invalid-feedback">
                                * <?= $err['color']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">登録</button>
                    </dev>
            </form>
        </div>
        </div>
    </div>
</main>
<?php require_once('elements/footer.php'); ?>
</body>
</html>

