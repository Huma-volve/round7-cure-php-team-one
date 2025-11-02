<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Reverb</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <h2>🔔 Doctor Dashboard — Real-Time Notifications</h2>
    <div id="notifications" style="margin-top:20px; font-size:18px;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorId = 5;

            if (window.Echo) {
                console.log("✅ Echo loaded successfully");

                window.Echo.channel(`doctor.${doctorId}`)
                    .listen('.booking.created', (event) => {
                        console.log("🎉 New booking received!", event.booking);
                    });

            } else {
                console.error("❌ window.Echo is undefined!");
            }
        });
    </script>
</body>
</html>
