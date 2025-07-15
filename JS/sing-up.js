function hyouji() {
    const selected = document.querySelector('input[name="gender"]:checked');
    if (selected) {
        console.log("選ばれた性別の値: " + selected.value);
        // または DOM に表示
        document.getElementById("gendar-result").textContent = "性別が選択されました";
        document.getElementById("gendar-result").style.display = "block";
    }
}