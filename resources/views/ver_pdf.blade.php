<!DOCTYPE html>
<html>
<head>
    <title>PDF Viewer</title>
    <style>
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
        }
        .print-btn {
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
<div class="controls">
    <button class="print-btn" onclick="printPDF()">Imprimir PDF</button>
</div>
<div class="pdf-container">
    <iframe
        id="pdf-viewer"
        src="https://docs.google.com/viewer?url={{ urlencode($pdfUrl) }}&embedded=true&toolbar=1"
        class="pdf-iframe"
        allowfullscreen>
    </iframe>
</div>

<script>
    function printPDF() {
        // Opción 1: Abrir la ventana de impresión de Google Docs
        const currentSrc = document.getElementById('pdf-viewer').src;
        const printUrl = currentSrc.replace('embedded=true', 'embedded=false');
        window.open(printUrl, '_blank');

        // Opción 2: Imprimir directamente el PDF original
        // window.open('{{ $pdfUrl }}', '_blank');

        window.close();
    }

    // Mejorar la carga del visor
    document.getElementById('pdf-viewer').onload = function() {
        // Intentar forzar la visibilidad de los controles de Google Docs
        const iframe = document.getElementById('pdf-viewer');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        if (iframeDoc) {
            const toolbarStyle = document.createElement('style');
            toolbarStyle.textContent = `
                    .toolbar { display: block !important; }
                    .toolbar-wrapper { display: block !important; }
                `;
            iframeDoc.head.appendChild(toolbarStyle);
        }
    };
</script>
</body>
</html>
