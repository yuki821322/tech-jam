<?php
// ========== joint.php (マルチタスク追加処理の例) ==========

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $project_id = $_POST['project_id'] ?? '';
    $multi_title = trim($_POST['multi_title'] ?? '');
    $multi_deadline = $_POST['multi_deadline'] ?? '';
    $subtasks = $_POST['subtasks'] ?? [];
    $creator = 'ユーザー名'; // 実際の実装では適切に設定

    if ($project_id !== '' && $multi_title !== '' && $multi_deadline !== '') {
        $task_csv = '../../CSV/jointtask.csv';
        $project_csv = '../../CSV/project.csv';

        // サブタスクを結合
        $subtasks_str = implode('|', array_filter($subtasks));

        // 新しいタスクデータ
        $new_task = [$project_id, $multi_title, $multi_deadline, $subtasks_str, $creator];

        // ヘッダーチェック
        $need_header = true;
        if (file_exists($task_csv) && filesize($task_csv) > 0) {
            $fp_check = fopen($task_csv, 'r');
            $first_line = fgetcsv($fp_check);
            fclose($fp_check);

            if ($first_line !== false) {
                $need_header = false;
            }
        }

        // タスクを追加
        if (($fp = fopen($task_csv, 'a')) !== false) {
            if ($need_header) {
                fputcsv($fp, ['project_id', 'task_title', 'deadline', 'subtasks', 'creator']);
            }
            fputcsv($fp, $new_task);
            fclose($fp);

            // プロジェクトを「作成済み」にマークする
            markProjectAsCreated($project_csv, $project_id);
        }

        header("Location: jointtask.php");
        exit;
    } else {
        echo "必要な情報が入力されていません。";
    }
}

// markProjectAsCreated関数を再定義（joint.phpでも使用）
function markProjectAsCreated($project_csv, $project_id)
{
    if (!file_exists($project_csv)) return;

    $rows = [];
    $header = [];

    if (($fp = fopen($project_csv, 'r')) !== false) {
        $header = fgetcsv($fp);
        while (($row = fgetcsv($fp)) !== false) {
            if (count($row) >= 1 && $row[0] === $project_id) {
                // is_created の列番号を探す
                $idx = array_search('is_created', $header);
                if ($idx !== false) {
                    $row[$idx] = 'true';
                }
            }
            $rows[] = $row;
        }
        fclose($fp);

        // ファイルに書き戻し
        if (($fpw = fopen($project_csv, 'w')) !== false) {
            fputcsv($fpw, $header);
            foreach ($rows as $row) {
                fputcsv($fpw, $row);
            }
            fclose($fpw);
        }
    }
}
