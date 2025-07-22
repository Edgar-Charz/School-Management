<?php
// Set timeout duration in seconds
$timeout_duration = 60;

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../php/login.php?timeout=true");
        exit();
    }
}

$_SESSION['last_activity'] = time();
?>
<script>
    const timeoutDuration = 60000; // 10 minutes in ms
    const warningTime = 30000; // 1 minute in ms
    const logoutUrl = "../php/logout.php";

    let countdownShown = false;
    let warningTimer;

    function showWarning() {
        if (!countdownShown) {
            countdownShown = true;
            let secondsLeft = 30;

            const alertBox = document.createElement("div");
            alertBox.style.position = "fixed";
            alertBox.style.bottom = "20px";
            alertBox.style.right = "20px";
            alertBox.style.padding = "15px";
            alertBox.style.backgroundColor = "#f44336";
            alertBox.style.color = "#fff";
            alertBox.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.3)";
            alertBox.style.zIndex = "9999";
            alertBox.style.borderRadius = "5px";
            alertBox.innerText = `Session will expire in ${secondsLeft} seconds...`;
            document.body.appendChild(alertBox);

            const countdown = setInterval(() => {
                secondsLeft--;
                alertBox.innerText = `Session will expire in ${secondsLeft} seconds...`;

                if (secondsLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = logoutUrl;
                }
            }, 1000);
        }
    }

    function resetTimer() {
        clearTimeout(warningTimer);
        countdownShown = false;

        // Remove existing alert if any
        const existingAlert = document.querySelector("#session-alert");
        if (existingAlert) existingAlert.remove();

        // Restart the warning timer
        warningTimer = setTimeout(showWarning, timeoutDuration - warningTime);
    }

    // Listen for user activity
    ['click', 'mousemove', 'keypress', 'scroll'].forEach(evt =>
        document.addEventListener(evt, resetTimer)
    );

    // Start the initial timer
    warningTimer = setTimeout(showWarning, timeoutDuration - warningTime);
</script>
