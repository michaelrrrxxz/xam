@extends('results.layout')

@section('title', 'Student Results Report')

@section('content')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 0.5px solid #444;
            padding: 4px 6px;
            text-align: center;
        }

        thead th {
            background: #f2f2f2;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }
    </style>

    <table>
        <thead>
            <tr>
                <th rowspan="3">ID No.</th>
                <th rowspan="3">Course</th>
                <th rowspan="3" style="width:200px">Name</th>
                <th rowspan="3" title="Raw Score">Raw Score</th>
                <th rowspan="3" title="Student Ability Index">SAI</th>
                <th colspan="2" title="Performance By Age">PBA</th>
                <th colspan="2" title="Performance By Grade">PBG</th>
                <th colspan="5">Verbal</th>
                <th colspan="5">Non-Verbal</th>
            </tr>
            <tr>
                <th rowspan="2" title="Percentile Rank">PR</th>
                <th rowspan="2" title="Stanine">S</th>
                <th rowspan="2" title="Percentile Rank">PR</th>
                <th rowspan="2" title="Stanine">S</th>
                <th colspan="2" title="Verbal Comprehension">VC</th>
                <th colspan="2" title="Verbal Reasoning">VR</th>
                <th rowspan="2">Total</th>
                <th colspan="2" title="Quantitative Reasoning">QR</th>
                <th colspan="2" title="Figural Reasoning">FR</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>S</th>
                <th>PC</th>
                <th>S</th>
                <th>PC</th>
                <th>S</th>
                <th>PC</th>
                <th>S</th>
                <th>PC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $result->student->id_number }}</td>
                <td>{{ $result->student->course }}</td>
                <td>{{ $result->student->last_name }}, {{ $result->student->first_name }} {{ $result->student->middle_name }}</td>
                <td>{{ $result->total_score }}</td>
                <td>{{ $result->sai_t }}</td>
                <td>{{ $result->pba_pr_t }}</td>
                <td>{{ $result->pba_s_t }}</td>
                <td>{{ $result->pbg_pr_t }}</td>
                <td>{{ $result->pbg_s_t }}</td>
                <td>{{ $result->verbal_comprehension }}</td>
                <td>{{ $result->verbal_comprehension_category }}</td>
                <td>{{ $result->verbal_reasoning }}</td>
                <td>{{ $result->verbal_reasoning_category }}</td>
                <td>{{ $result->verbal }}</td>
                <td>{{ $result->quantitative_reasoning }}</td>
                <td>{{ $result->quantitative_reasoning_category }}</td>
                <td>{{ $result->figural_reasoning }}</td>
                <td>{{ $result->figural_reasoning_category }}</td>
                <td>{{ $result->non_verbal }}</td>
            </tr>
        </tbody>
    </table>
@endsection
