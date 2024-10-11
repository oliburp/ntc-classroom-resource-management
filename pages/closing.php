
</div>
</body>
<script>
    function logout() {
        if (confirm("Are you sure you want to logout?")) {
            // Make an AJAX request to your logout endpoint
            $.ajax({
                url: '../backend/logout.php', // Adjust the path to your logout script
                method: 'POST',
                success: function(response) {
                    // On success, redirect to the login page or display a message
                    window.location.href = '../pages/login.php'; // Redirect to login page
                },
                error: function(xhr, status, error) {
                    alert("Error logging out: " + error);
                }
            });
        }
    }
</script>