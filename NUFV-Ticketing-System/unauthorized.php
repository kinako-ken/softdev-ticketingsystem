<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
</head>
<body>

    <div class="container" style="text-align: center; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <h1 style="color: #ff0000; font-size: 32px; margin-bottom: 20px;">Unauthorized Access</h1>
        <p style="color: #333; font-size: 18px; margin-bottom: 20px;">You don't have permission to access this page.</p>
        <button onclick="goBack()" style="background-color: #007bff; color: #fff; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px; cursor: pointer;">Go Back</button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>