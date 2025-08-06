<?php
// ========== jointtask-delete.php (タスク削除処理) ==========

// プロジェクトにタスクが追加されたことをマークする関数
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

// ファイルパス
$task_csv = '../../CSV/jointtask.csv';
$project_csv = '../../CSV/project.csv';

// パラメータ取得
if (!isset($_GET['id'])) {
    echo "不正なアクセスです。";
    exit;
}

$id_parts = explode('|', $_GET['id'], 2);
if (count($id_parts) < 2) {
    echo "不正なパラメータです。";
    exit;
}

list($project_id, $task_title) = $id_parts;

// jointtask.csv 読み込み
$tasks = [];
$header = [];
if (file_exists($task_csv) && ($fp = fopen($task_csv, 'r')) !== false) {
    $header = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        $tasks[] = $row;
    }
    fclose($fp);
} else {
    echo "タスクデータが見つかりません。";
    exit;
}

// 削除対象のタスクを除く
$tasks_new = [];
$task_deleted = false;

foreach ($tasks as $row) {
    if (count($row) >= 5 && $row[0] === $project_id && $row[1] === $task_title) {
        // このタスクは削除対象なのでスキップ
        $task_deleted = true;
        continue;
    }
    $tasks_new[] = $row;
}

if (!$task_deleted) {
    echo "削除対象のタスクが見つかりませんでした。";
    exit;
}

// jointtask.csv に書き込み（上書き保存）
if (($fp = fopen($task_csv, 'w')) !== false) {
    if (!empty($header)) {
        fputcsv($fp, $header);
    }
    foreach ($tasks_new as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
} else {
    echo "タスクデータの書き込みに失敗しました。";
    exit;
}

// プロジェクトにまだタスクが残っているかチェック
$project_tasks_remaining = false;
foreach ($tasks_new as $row) {
    if (count($row) >= 5 && $row[0] === $project_id) {
        $project_tasks_remaining = true;
        break;
    }
}

// プロジェクトにタスクが残っていない場合のみ、プロジェクト削除を検討
if (!$project_tasks_remaining) {
    // project.csv 読み込み
    $projects = [];
    $project_header = [];

    if (file_exists($project_csv) && ($fp = fopen($project_csv, 'r')) !== false) {
        $project_header = fgetcsv($fp);
        while (($row = fgetcsv($fp)) !== false) {
            $projects[] = $row;
        }
        fclose($fp);

        // プロジェクトの is_created 列のインデックス取得
        $is_created_index = array_search('is_created', $project_header);
        $id_index = array_search('project_id', $project_header);

        if ($is_created_index !== false && $id_index !== false) {
            $new_projects = [];
            foreach ($projects as $row) {
                if (count($row) > max($id_index, $is_created_index) && $row[$id_index] === $project_id) {
                    // 対象プロジェクト。is_createdがtrueなら削除（スキップ）
                    if (strtolower(trim($row[$is_created_index])) === 'true') {
                        // 削除するので何もしない（除外）
                        continue;
                    }
                }
                $new_projects[] = $row;
            }

            // project.csv 更新
            if (($fp = fopen($project_csv, 'w')) !== false) {
                fputcsv($fp, $project_header);
                foreach ($new_projects as $row) {
                    fputcsv($fp, $row);
                }
                fclose($fp);
            }
        }
    }
}

// 削除後は一覧に戻る
header("Location: jointtask.php");
exit;
