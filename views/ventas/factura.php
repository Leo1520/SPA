<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= str_pad($factura['id'], 6, '0', STR_PAD_LEFT) ?> - SPA Las América</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 20px;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .factura-container {
                box-shadow: none !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }

        /* Estilos de la factura */
        .factura-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .factura-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #2c5f4f;
        }

        .factura-logo h1 {
            color: #2c5f4f;
            margin: 0 0 0.5rem 0;
        }

        .factura-logo p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }

        .factura-info {
            text-align: right;
        }

        .factura-numero {
            font-size: 1.5em;
            font-weight: bold;
            color: #2c5f4f;
            margin-bottom: 0.5rem;
        }

        .factura-datos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .factura-datos h3 {
            color: #2c5f4f;
            margin: 0 0 1rem 0;
            font-size: 1em;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }

        .factura-datos p {
            margin: 0.5rem 0;
        }

        .factura-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .factura-tabla th {
            background: #2c5f4f;
            color: white;
            padding: 0.75rem;
            text-align: left;
        }

        .factura-tabla td {
            padding: 0.75rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .factura-tabla tfoot td {
            font-weight: bold;
            font-size: 1.1em;
            background: #f8f9fa;
        }

        .factura-footer {
            margin-top: 3rem;
            padding-top: 1rem;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }

        .btn-imprimir {
            background: #2c5f4f;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            margin-bottom: 1rem;
            display: inline-block;
            text-decoration: none;
        }

        .btn-imprimir:hover {
            background: #1f4538;
        }
    </style>
</head>
<body>
    <!-- Botones de acción (no se imprimen) -->
    <div class="no-print" style="text-align: center; margin: 1rem 0;">
        <button onclick="window.print()" class="btn-imprimir">🖨️ Imprimir Factura</button>
        <a href="index.php?page=ventas&action=show&id=<?= htmlspecialchars($factura['id_venta']) ?>" class="btn-imprimir" style="background: #6c757d;">
            ← Volver a Venta
        </a>
    </div>

    <!-- Contenedor de la Factura -->
    <div class="factura-container">
        <!-- Encabezado -->
        <div class="factura-header">
            <div class="factura-logo">
                <h1>🌿 SPA LAS AMÉRICA</h1>
                <p>Centro de Relajación y Bienestar</p>
                <p>Teléfono: (123) 456-7890</p>
                <p>Email: info@spalasamerica.com</p>
            </div>
            <div class="factura-info">
                <div class="factura-numero">FACTURA</div>
                <div class="factura-numero"><?= date('Y') ?>-<?= str_pad($factura['id'], 6, '0', STR_PAD_LEFT) ?></div>
                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></p>
            </div>
        </div>

        <!-- Datos del Cliente y Venta -->
        <div class="factura-datos">
            <div>
                <h3>DATOS DEL CLIENTE</h3>
                <p><strong>Nombre/Razón Social:</strong><br><?= htmlspecialchars($factura['razon_social']) ?></p>
                <p><strong>NIT/CI:</strong> <?= htmlspecialchars($factura['nit_cliente']) ?></p>
            </div>
            <div>
                <h3>DATOS DE LA VENTA</h3>
                <p><strong>Venta #:</strong> <?= htmlspecialchars($factura['id_venta']) ?></p>
                <p><strong>Reserva #:</strong> <?= htmlspecialchars($factura['id_reserva']) ?></p>
                <p><strong>Fecha de Servicio:</strong> <?= date('d/m/Y', strtotime($factura['fecha_servicio'])) ?></p>
            </div>
        </div>

        <!-- Detalle de Servicios -->
        <h3 style="color: #2c5f4f; margin-bottom: 1rem;">DETALLE DE SERVICIOS</h3>
        <table class="factura-tabla">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Terapeuta</th>
                    <th style="text-align: center;">Cant.</th>
                    <th style="text-align: right;">Precio Unit.</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detallesVenta as $detalle): ?>
                    <tr>
                        <td><?= htmlspecialchars($detalle['servicio_nombre']) ?></td>
                        <td><?= htmlspecialchars($detalle['empleado_nombre'] ?? 'N/A') ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($detalle['cantidad']) ?></td>
                        <td style="text-align: right;">Bs. <?= number_format($detalle['precio_unitario'], 2) ?></td>
                        <td style="text-align: right;">Bs. <?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;">Bs. <?= number_format($factura['total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Total en letras (opcional) -->
        <p style="margin: 1rem 0; font-style: italic; color: #666;">
            <strong>Estado:</strong> PAGADO
        </p>

        <!-- Pie de página -->
        <div class="factura-footer">
            <p>Gracias por su preferencia</p>
            <p>Este documento es una factura válida</p>
            <p style="margin-top: 1rem; font-size: 0.8em;">
                Generado el <?= date('d/m/Y H:i:s') ?>
            </p>
        </div>
    </div>

    <script>
        // Auto-focus en el botón de imprimir
        document.addEventListener('DOMContentLoaded', function() {
            // Opcional: abrir diálogo de impresión automáticamente
            // window.print();
        });
    </script>
</body>
</html>
