<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ "Factura-{$bill->id}-{$bill->created_at->format('dmY')}" }}</title>
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
                    <h2>{{ $organization->name }}</h2><h3 class="pull-right">Factura # {{ $bill->id }}</h3>
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
                            @if($bill->client)
                                {{ $bill->client->name }}<br>
                                {{ $bill->client->id_card }}
                            @else
                                Cliente al contado <br>
                            @endif
                        </address>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <address>
                            <strong>Tipo de factura:</strong><br>
                            {{ $bill->bill_type }}
                        </address>
                    </div>
                    <div class="col-xs-6 text-right">
                        <address>
                            <strong>Fecha:</strong><br>
                            {{ $bill->created_at->format('F d, Y') }}<br><br>
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
                        <td><strong>Descripción</strong></td>
                        <td class="text-center"><strong>Costo</strong></td>
                        <td class="text-center"><strong>Cantidad</strong></td>
                        <td class="text-right"><strong>Total</strong></td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bill->products as $product)
                        <tr>
                            <td>
                                {{ $product->name }} <br>
                                {{ $product->description }}
                            </td>
                            <td class="text-center">${{ number_format($product->pivot->cost, 2) }}</td>
                            <td class="text-center">{{ $product->pivot->quantity }}</td>
                            <td class="text-right">${{ number_format($product->pivot->sub_total) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="thick-line"></td>
                        <td class="thick-line"></td>
                        <td class="thick-line text-right"><strong>Sub total</strong></td>
                        <td class="thick-line text-right">${{ number_format($sub_total, 2) }}</td>
                    </tr>
                    @if($bill->discount > 0)
                        <tr>
                            <td class="no-line"></td>
                            <td class="no-line"></td>
                            <td class="no-line text-right"><strong>Descuento</strong></td>
                            <td class="no-line text-right">{{ $bill->discount }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-right"><strong>Pagado hasta la fecha</strong></td>
                        <td class="no-line text-right">${{ number_format($paid_date, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-right"><strong>Total</strong></td>
                        <td class="no-line text-right">${{ number_format($total, 2) }}</td>
                    </tr>
                    @if($due_amount > 0)
                        <tr>
                            <td class="no-line"></td>
                            <td class="no-line"></td>
                            <td class="no-line text-right"><strong>Balance despues del pago</strong></td>
                            <td class="no-line text-right">${{ number_format($due_amount, 2) }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </body>
</html>