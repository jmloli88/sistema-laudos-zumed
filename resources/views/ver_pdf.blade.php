<!DOCTYPE html>
<html>
<head>
    <title>PDF Viewer</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .pdf-container {
            width: 100%;
            height: 90vh;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .pdf-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .controls {
            padding: 10px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 15px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .print-btn {
            background: #4CAF50;
        }
        .print-btn:hover {
            background: #45a049;
        }
        .download-btn {
            background: #2196F3;
        }
        .download-btn:hover {
            background: #1976D2;
        }
        .back-btn {
            background: #757575;
        }
        .back-btn:hover {
            background: #616161;
        }
    </style>
</head>
<body>
<div class="controls">
    <button class="btn print-btn" onclick="printPDF()">Imprimir PDF</button>
    <a href="{{ $pdfUrl }}" download class="btn download-btn">Descargar PDF</a>
    <button class="btn back-btn" onclick="window.history.back()">Volver</button>
</div>
<div class="pdf-container">
    <iframe
        id="pdf-viewer"
        src="{{ $pdfUrl }}"
        class="pdf-iframe"
        allowfullscreen>
    </iframe>
</div>

<script>
    function printPDF() {
        // Abrir el PDF en nueva ventana para imprimir
        window.open('{{ $pdfUrl }}', '_blank');
    }
</script>
</body>
</html>
