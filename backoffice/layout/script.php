<script>

// searching in add service dropdown 
        function searchServices() {
            let input, filter, options, option, i, txtValue;
            input = document.getElementById('Services-input');
            filter = input.value.toUpperCase();
            options = document.getElementById('Services-Options').getElementsByTagName('option');

            for (i = 0; i < options.length; i++) {
                option = options[i];
                txtValue = option.textContent || option.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }

// searching in edit service dropdown 
        function editSearchServices() {
            let input, filter, options, option, i, txtValue;
            input = document.getElementById('Services-input-E');
            filter = input.value.toUpperCase();
            options = document.getElementById('Services-Options-E').getElementsByTagName('option');

            for (i = 0; i < options.length; i++) {
                option = options[i];
                txtValue = option.textContent || option.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }
    
// searching in doctor field 
    function searchDoctor() {
            let input, filter, options, option, i, txtValue;
            input = document.getElementById('doctor-input');
            filter = input.value.toUpperCase();
            options = document.getElementById('doctor-Options').getElementsByTagName('option');

            for (i = 0; i < options.length; i++) {
                option = options[i];
                txtValue = option.textContent || option.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }

        function search() {
            let input, filter, options, option, i, txtValue;
            input = document.getElementById('Search-input');
            filter = input.value.toUpperCase();
            options = document.getElementById('Search-Options').getElementsByTagName('tbody');

            for (i = 0; i < options.length; i++) {
                option = options[i];
                txtValue = option.textContent || option.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }
        
</script>


