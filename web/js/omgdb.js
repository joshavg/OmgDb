document.getElementById('select-all').addEventListener('click', function (e) {
    const table = document.getElementsByClassName('import-table')[0];
    const rowBoxes = table.getElementsByClassName('use-checkbox');

    for(const box of rowBoxes) {
        box.checked = e.target.checked;
    }
});
