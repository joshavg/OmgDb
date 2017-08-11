document.getElementById('select-all').addEventListener('click', function (e) {
    const rowBoxes = document.getElementsByClassName('use-checkbox');

    for (const box of rowBoxes) {
        box.checked = e.target.checked;
    }
});

const useTrs = document.getElementsByClassName('use-tr');
for (const tr of useTrs) {
    tr.addEventListener('click', function (e) {
        for (const box of tr.getElementsByClassName('use-checkbox')) {
            box.checked = !box.checked;
        }
    });
}
