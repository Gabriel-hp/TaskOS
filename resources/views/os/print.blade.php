<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O.S. {{ $os->protocolo }} - TaskOS</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            position: relative;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }
        
        .company-subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .protocol-info {
            position: absolute;
            top: 0;
            right: 0;
            text-align: right;
            font-size: 10px;
        }
        
        .protocol-number {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .os-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 6px;
            border: 1px solid #000;
            margin: 8px 0;
            text-align: center;
        }
        
        .info-section {
            margin-bottom: 12px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 6px;
            align-items: center;
            min-height: 20px;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 100px;
            margin-right: 8px;
            font-size: 10px;
        }
        
        .info-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding: 2px 4px;
            min-height: 16px;
            font-size: 11px;
        }
        
        .two-column {
            display: flex;
            gap: 15px;
            margin-bottom: 6px;
        }
        
        .column {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .column .info-label {
            min-width: 80px;
        }
        
        .service-section {
            margin: 15px 0;
        }
        
        .service-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .service-content {
            border: 1px solid #000;
            min-height: 60px;
            padding: 6px;
            font-size: 10px;
            line-height: 1.4;
            background: white;
        }
        
        .service-content.large {
            min-height: 80px;
        }
        
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .signature-box {
            text-align: center;
            width: 150px;
            margin-bottom: 20px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 3px;
            height: 30px;
        }
        
        .signature-label {
            font-size: 9px;
            font-weight: bold;
        }
        
        .final-signature {
            margin-top: 25px;
            text-align: center;
        }
        
        .final-signature .signature-box {
            margin: 0 auto;
            width: 200px;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .container {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="protocol-info">
                <div class="protocol-number">Protocolo: {{ $os->protocolo }}</div>
                <div>DATA DE EMISSÃO: {{ $os->data_hora_inicio->format('d/m/Y') }}</div>
            </div>
            
            <div class="company-name">Logic Pro</div>
            <div class="company-subtitle">soluções tecnológicas</div>
        </div>
        
        <div class="os-title">ORDEM DE SERVIÇO (O.S.)</div>
        
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">CLIENTE:</span>
                <span class="info-value">{{ $os->cliente_nome }}</span>
            </div>
            
            <div class="two-column">
                <div class="column">
                    <span class="info-label">CNPJ:</span>
                    <span class="info-value"></span>
                </div>
                <div class="column">
                    <span class="info-label">Numero O.S:</span>
                    <span class="info-value">{{ $os->protocolo }}</span>
                </div>
            </div>
            
            <div class="info-row">
                <span class="info-label">CONTATO:</span>
                <span class="info-value"></span>
            </div>
            
            <div class="info-row">
                <span class="info-label">ENDEREÇO:</span>
                <span class="info-value">{{ $os->endereco }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">DESIGNAÇÃO:</span>
                <span class="info-value">{{ $os->designacao }}</span>
            </div>
            
            <div class="two-column">
                <div class="column">
                    <span class="info-label">Modelo do Equipamento:</span>
                    <span class="info-value"></span>
                </div>
                <div class="column">
                    <span class="info-label">HORA INÍCIO:</span>
                    <span class="info-value">{{ $os->data_hora_inicio->format('H:i') }}</span>
                </div>
            </div>
            
            <div class="two-column">
                <div class="column">
                    <span class="info-label">Num. de Série:</span>
                    <span class="info-value"></span>
                </div>
                <div class="column">
                    <span class="info-label">HORA FIM:</span>
                    <span class="info-value"></span>
                </div>
            </div>
            
            <div class="info-row">
                <span class="info-label">Ativação:</span>
                <span class="info-value"></span>
            </div>
        </div>
        
        <div class="service-section">
            <div class="service-title">Descrição do serviço:</div>
            <div class="service-content large">{{ $os->motivo }}</div>
        </div>
        
        <div class="service-section">
            <div class="service-title">Observação:</div>
            <div class="service-content">{{ $os->observacao ?? '' }}</div>
        </div>
        
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">TÉCNICO</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">TÉCNICO NOC</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">NOC RESPONSÁVEL</div>
            </div>
        </div>
        
        <div class="final-signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">CONTRATANTE/REPRESENTANTE</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
