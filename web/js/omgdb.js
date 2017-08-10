document.getElementById('select-all').addEventListener('click', function (e) {
    const rowBoxes = document.getElementsByClassName('use-checkbox');

    for(const box of rowBoxes) {
        box.checked = e.target.checked;
    }
});
