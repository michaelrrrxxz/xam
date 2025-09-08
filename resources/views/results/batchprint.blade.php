<!DOCTYPE html>
<html>
<head>
    <title>Batch Results Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 95%; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: center; font-size: 12px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h2>Batch Results Report</h2>
            <p><strong>Batch:</strong> {{ $results->first()->batch->name ?? '' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID No.</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Total Score</th>
                    <th>SAI</th>
                    <th>Verbal</th>
                    <th>Non-Verbal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr>
                    <td>{{ $result->student->id_number }}</td>
                    <td>{{ $result->student->last_name }}, {{ $result->student->first_name }} {{ $result->student->middle_name }}</td>
                    <td>{{ $result->student->course }}</td>
                    <td>{{ $result->total_score }}</td>
                    <td>{{ $result->sai_t }}</td>
                    <td>{{ $result->verbal }}</td>
                    <td>{{ $result->non_verbal }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
