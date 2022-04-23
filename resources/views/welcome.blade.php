<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <!-- Styles -->
    </head>
    <body class="antialiased">
    <table id="example" class="display" style="width:100%">
        <thead>
        <tr>
            <th>ValuteID</th>
            <th>NumCode</th>
            <th>CharCode</th>
            <th>Nominal</th>
            <th>Value</th>
            <th>date</th>
        </tr>
        </thead>
        <tbody>
        @if(count($valutes) > 0)
            @foreach($valutes as $item)
                <tr>
                    <td>{{$item->valute_id}}</td>
                    <td>{{ $item->num_code }}</td>
                    <td>{{ $item->char_code }}</td>
                    <td>{{ $item->nominal }}</td>
                    <td>{{ $item->value }}</td>
                    <td>{{$item->date}}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
        </tfoot>
    </table>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
    </script>
    </body>
</html>
