<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qamla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .light-blue-bg {
            width: 60%;
            margin: 0 auto;
            background-color: #e6f3ff;
            border-radius: 20px;
            padding: 2rem 3rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide {
            animation: slideIn 0.8s ease-out forwards;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .rocket {
            animation: bounce 2s infinite ease-in-out;
            display: inline-block;
        }

        .text-glow {
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">
    <!-- Main Section -->
    <div class="light-blue-bg">
        <div class="text-center animate-slide">
            <div class="flex items-center justify-center gap-3 mb-4">
                <span class="text-4xl rocket">🚀</span>
                <h1 class="text-4xl font-bold text-gray-800 text-glow">
                    Qamla is Running!
                </h1>
            </div>
            <p class="text-gray-600 text-lg">Your application is up and running smoothly.</p>
        </div>
    </div>

    <!-- Notification Alert -->
    <div id="notification"
        class="hidden fixed top-5 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-5 py-3 rounded-lg shadow-lg">
        <span id="notification-message"></span>
    </div>

    <script type="module">
        window.Echo.private("employer.1")
            .listen("NotifyEvent", (event) => {
                console.log("Notification received:", event);

                const notificationBox = document.getElementById("notification");
                const notificationMessage = document.getElementById("notification-message");

                notificationMessage.textContent = event.message || "New notification!";
                notificationBox.classList.remove("hidden");

                setTimeout(() => {
                    notificationBox.classList.add("hidden");
                }, 3000);
            })
            .error((error) => {
                console.error("WebSocket Subscription Error:", error);
            });
    </script>
</body>

</html>
