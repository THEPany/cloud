<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ "Recivo-{$payment->bill->id}-{$payment->bill->created_at->format('dmY')}" }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">
    <style>
        .invoice-title h2, .invoice-title h3 {
            display: inline-block;
        }

        .table > tbody > tr > .no-line {
            border-top: none;
        }

        .table > thead > tr > .no-line {
            border-bottom: none;
        }

        .table > tbody > tr > .thick-line {
            border-top: 2px solid;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" defer></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" defer></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="invoice-title">
                <h2>{{ $organization->name }}</h2><h3 class="pull-right">Factura # {{ $payment->id }}</h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-6">
                    <address>
                        <strong>{{ $organization->name }}:</strong><br>
                        {{ $organization->phone }} <br>
                        {{ $organization->address }} <br>
                    </address>
                </div>
                <div class="col-xs-6 text-right">
                    <address>
                        <strong>Consumidor Final:</strong><br>
                        @if($payment->bill->client)
                            {{ $payment->bill->client->name }}<br>
                            {{ $payment->bill->client->id_card }}
                        @else
                            Cliente al contado <br>
                        @endif
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-right">
                    <address>
                        <strong>Fecha:</strong><br>
                        {{ $payment->created_at->format('F d, Y') }}<br><br>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <td><strong>Factura</strong></td>
                    <td><strong>Fecha Creacion</strong></td>
                    <td><strong>Fecha de expiracion</strong></td>
                    <td><strong>Cantidad Abonada</strong></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $payment->bill->id }}</td>
                    <td>{{ $payment->bill->created_at->format('d-m-Y') }}</td>
                    <td>{{ $payment->bill->expired_at->format('d-m-Y') }}</td>
                    <td>${{ number_format($payment->paid_out, 2) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>