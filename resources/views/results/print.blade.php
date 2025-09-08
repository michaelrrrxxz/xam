<!DOCTYPE html>
<html>
<head>
    <title>Result Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 800px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h2>Student Result Report</h2>
        </div>

        <p><strong>ID No:</strong> {{ $result->student->id_number }}</p>
        <p><strong>Name:</strong> {{ $result->student->last_name }}, {{ $result->student->first_name }} {{ $result->student->middle_name }}</p>
        <p><strong>Course:</strong> {{ $result->student->course }}</p>
        <p><strong>Batch:</strong> {{ $result->batch->name }}</p>

        <table>
            <tr>
                <th>Total Score</th>
                <td>{{ $result->total_score }}</td>
            </tr>
            <tr>
                <th>SAI</th>
                <td>{{ $result->sai_t }}</td>
            </tr>
            <tr>
                <th>Verbal</th>
                <td>{{ $result->verbal }}</td>
            </tr>
            <tr>
                <th>Non-Verbal</th>
                <td>{{ $result->non_verbal }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
