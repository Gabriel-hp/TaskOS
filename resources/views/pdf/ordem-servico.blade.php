<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Ordem de Serviço</title>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: middle;
    }
    .no-border {
        border: none;
    }
    .os {
        text-align: center;
        width: 100%;
    }
    .bold {
        font-weight: bold;
    }
    .header-logo {
        display: flex;
        align-items: center;
    }
    .header-logo img {
        height: 40px;
    }
    .assinatura {
        height: 50px;
    }
     .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            font-size: 8px;
            color: #666;
        }
</style>
</head>
<body>

<table>
        <td class="center bold">
            <img src="{{ public_path('logo.png') }}" alt="Logo TaskOS" style="height: 100px;">
        </td>
        
        <td class="center bold os">ORDEM DE SERVIÇO (O.S.)</td>
        <td class="center bold">Protocolo {{ $protocoloNum }}</td>
    </tr>
</table>


<table>
    <tr>
        <td class="bold" >CLIENTE</td>
        <td colspan="5"> {{ $ordemServico->cliente ?: $evento->title }}</td>
    </tr>
    <tr>
        <td class="bold">CNPJ</td>
        <td colspan="5">{{ $ordemServico->cnpj ?: '' }}</td>
    </tr>
    <tr>
        <td class="bold">ENDEREÇO</td>
        <td colspan="5">{{ $ordemServico->endereco_cliente ?: $evento->endereco }}</td>
    </tr>
    <tr>
        <td class="bold">CONTATO</td>
        <td colspan="5">{{ $ordemServico->contato_cliente ?: '' }}</td>
    </tr>
    <tr>
        <td class="bold">DESIGNAÇÃO</td>
        <td></td>
        <td class="bold">HORA INÍCIO </td>
        <td>{{ $ordemServico->hora_inicio ?: $evento->start->format('H:i') }}</td>
        <td  class="bold" colspan="2"> &nbsp;&nbsp; HORA FIM: </td>
    </tr>
</table>

<table>
    <tr class="equipamento">
        <td class="bold" style="width: 10%;">Modelo</td>
        <td></td>
                <td class="bold" style="width: 10%;">Nº Serial</td>
        <td></td>
    </tr>

    <tr class="equipamento">
        <td class="bold" style="width: 10%;">Modelo</td>
        <td></td>
                <td class="bold"  style="width: 10%;">Nº Serial</td>
        <td></td>
    </tr>

    <tr class="equipamento">
        <td class="bold" style="width:10%;">Modelo</td>
        <td></td>
                <td class="bold"  style="width: 10%;">Nº Serial</td>
        <td></td>
    </tr>
    <tr class="equipamento">
        <td class="bold" style="width:10%;">Modelo</td>
        <td></td>
                <td class="bold"  style="width: 10%;">Nº Serial</td>
        <td></td>
    </tr>

</table>

<table>
    <tr>
        <th class="center">Descrição do serviço</th>
    </tr>
    <tr>
        <td style="height: 60px; text-align: center;">{{ $ordemServico->designacao ?: $evento->assunto }}</td>
    </tr>
</table>

<table>
    <tr>
        <th class="center">Observação</th>
    </tr>
    <tr>
        <td style="height: 110px;">{{ $ordemServico->observacao ?: $ordemServico->materiais }}</td>
    </tr>
</table>

<table>
    <tr>
        <th class="center">Assinaturas</th>
    </tr>
    <tr>
        <td>
            <table style="width: 100%; border: none; text-align: center;">
                <tr>
                    <td class="assinatura center  " style=" width: 50%;">
                        <br><br>
                        TÉCNICO NOC RESPONSÁVEL
                         <div>{{ $responsavel->name }}</div>
                    </td>
                    <td class="assinatura center " style=" width: 50%;">
                        <br><br>
                        CONTRATANTE / REPRESENTANTE
                    </td>
                   
                </tr>
            </table>
             <div class="footer">
                        Documento gerado em {{ now()->format('d/m/Y H:i:s') }}
                    </div>
        </td>
    </tr>
</table>

</body>
</html>
