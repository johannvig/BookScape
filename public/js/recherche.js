document.getElementById('toggleFilter').addEventListener('click', function() {
    var filtres = document.getElementById('filtres');
    if (filtres.style.display === 'none') {
        filtres.style.display = 'block';
    } else {
        filtres.style.display = 'none';
    }
});
