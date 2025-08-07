function handleTaskCheck(checkbox) {
    const projectId = checkbox.dataset.projectId;
    const taskIndex = checkbox.dataset.taskIndex;
    const isDone = checkbox.checked ? '1' : '';

    // DOM操作
    const taskItem = checkbox.closest('.jointtask-item');
    if (checkbox.checked) {
        taskItem.classList.add('done-task');
    } else {
        taskItem.classList.remove('done-task');
    }

    // 保存処理
    fetch('task-check.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `project_id=${projectId}&task_index=${taskIndex}&done=${isDone}`
    }).then(response => {
        if (response.ok) {
            updateProgressBar();
        }
    });
}

function updateProgressBar() {
    const allTasks = document.querySelectorAll('.jointtask-item');
    const doneTasks = document.querySelectorAll('.jointtask-item.done-task');
    const percent = allTasks.length > 0 ? Math.round((doneTasks.length / allTasks.length) * 100) : 0;

    const progress = document.querySelector('#file');
    const percentText = document.querySelector('.progress p');

    if (progress && percentText) {
        progress.value = percent;
        percentText.textContent = percent + '%';
    }
}