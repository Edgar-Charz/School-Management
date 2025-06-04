<?php
// Set timeout duration
$timeout_duration = 600;

// Check last activity
if (isset($_SESSION['last_activity'])) {
    
    // Calculate the session lifetime
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {

        // Last request was more than timeout ago - destroy session
        session_unset();
        session_destroy();
        header("Location: ../php/login.php?timeout=true");
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>

<script>
    const timeoutDuration = 600000; // 
    const warningTime = 10000; // 
    const logoutUrl = "../php/logout.php";

    let countdownShown = false;

    let warningTimer = setTimeout(() => {
        if (!countdownShown) {
            countdownShown = true;
            let secondsLeft = 10;
            const alertBox = document.createElement("div");

            alertBox.style.position = "fixed";
            alertBox.style.bottom = "20px";
            alertBox.style.right = "20px";
            alertBox.style.padding = "15px";
            alertBox.style.backgroundColor = "#f44336";
            alertBox.style.color = "#fff";
            alertBox.style.boxShadow = "0 0 10px rgba(0, 0, 0, 0.3)";
            alertBox.style.zIndex = "9999";
            alertBox.style.innerText = 'Session will expire in ${secondsLeft} seconds...';
            document.body.appendChild(alertBox);

            let countdown = setInterval(() => {
                secondsLeft --;
                alertBox.innerText = 'Session will expire in ${secondsLeft} seconds...';
                if (secondsLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = logoutUrl;
                }
            }, 1000);
        } 
    }, timeoutDuration - warningTime);

    // Reset timer on user activity
    ['click', 'mousemove', 'keypress', 'scroll'].forEach(evt => document.addEventListener(evt, () => {
        clearTimeout(warningTimer);
    }));
</script>