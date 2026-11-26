document.addEventListener('DOMContentLoaded', function() {

    // AJAX live search
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        const dropdown = document.createElement('div');
        dropdown.className = 'list-group position-absolute w-100 shadow';
        dropdown.style.zIndex = '1000';
        dropdown.style.display = 'none';
        searchInput.parentElement.style.position = 'relative';
        searchInput.parentElement.appendChild(dropdown);

        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const q = this.value.trim();
            if (q.length < 2) {
                dropdown.style.display = 'none';
                return;
            }

            timeout = setTimeout(function() {
                fetch('/thesismatch/ajax/search.php?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(results => {
                        dropdown.innerHTML = '';
                        if (results.length === 0) {
                            dropdown.style.display = 'none';
                            return;
                        }
                        results.forEach(function(topic) {
                            const item = document.createElement('a');
                            item.href = '/thesismatch/topic_detail.php?id=' + topic.id;
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = '<strong>' + topic.title + '</strong>' +
                                '<br><small class="text-muted">' +
                                topic.subject_name + ' · prof. ' + topic.professor_name +
                                '</small>';
                            dropdown.appendChild(item);
                            
                        });
                        dropdown.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    // role izbira na registraciji
    const roleRadios = document.querySelectorAll('.role-radio');
    if (roleRadios.length > 0) {
        roleRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                
            });
        });
    }

});