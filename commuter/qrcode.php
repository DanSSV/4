<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://unpkg.com/@zxing/library@0.19.1"></script>
</head>
<body>
    <h1>QR Code Scanner</h1>
    <p>Scan a QR code to see the result:</p>
    <video id="qr-video" width="400" height="300"></video>
    <div id="result"></div>

    <script>
        // Function to handle the QR code scanning
        const videoElement = document.getElementById('qr-video');
        const resultElement = document.getElementById('result');

        const codeReader = new ZXing.BrowserQRCodeReader();

        codeReader
            .decodeFromVideoDevice(undefined, 'qr-video', (result, err) => {
                if (result) {
                    resultElement.innerHTML = `QR Code Result: ${result.text}`;
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.error('QR Code scan error:', err);
                    resultElement.innerHTML = 'Error scanning QR code.';
                }
            })
            .catch(error => {
                console.error('Error accessing the camera:', error);
                resultElement.innerHTML = 'Error accessing the camera.';
            });
    </script>
</body>
</html>
