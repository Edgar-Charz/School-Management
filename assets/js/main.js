
        const dropdownBtn = document.getElementById('profileDropdownBtn');
        const dropdown = document.getElementById('profileDropdown');
        dropdownBtn.onclick = function(e) {
            e.preventDefault();
            dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
        };
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== dropdownBtn && !dropdownBtn.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    