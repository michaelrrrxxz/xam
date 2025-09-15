@extends('results.layout')

@section('title', 'Batch Results Report')

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
         .batch-info {
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>

    <div class="batch-info">
        <div>Batch: {{ $batch->name ?? 'N/A' }}</div>
        <div>Description: {{ $batch->description ?? 'N/A' }}</div>
    </div>
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
            @foreach($result as $r)
                <tr>
                    <td>{{ $r->student->id_number }}</td>
                    <td>{{ $r->student->course }}</td>
                    <td>{{ $r->student->last_name }}, {{ $r->student->first_name }} {{ $r->student->middle_name }}</td>
                    <td>{{ $r->total_score }}</td>
                    <td>{{ $r->sai_t }}</td>
                    <td>{{ $r->pba_pr_t }}</td>
                    <td>{{ $r->pba_s_t }}</td>
                    <td>{{ $r->pbg_pr_t }}</td>
                    <td>{{ $r->pbg_s_t }}</td>
                    <td>{{ $r->verbal_comprehension }}</td>
                    <td>{{ $r->verbal_comprehension_category }}</td>
                    <td>{{ $r->verbal_reasoning }}</td>
                    <td>{{ $r->verbal_reasoning_category }}</td>
                    <td>{{ $r->verbal }}</td>
                    <td>{{ $r->quantitative_reasoning }}</td>
                    <td>{{ $r->quantitative_reasoning_category }}</td>
                    <td>{{ $r->figural_reasoning }}</td>
                    <td>{{ $r->figural_reasoning_category }}</td>
                    <td>{{ $r->non_verbal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
