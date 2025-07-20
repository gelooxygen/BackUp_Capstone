<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; font-size: 12px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Attendance Summary</h2>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Student</th>
                @foreach($days as $day)
                    <th style="text-align:center">{{ $day }}</th>
                @endforeach
                <th style="text-align:center">Present</th>
                <th style="text-align:center">Total</th>
                <th style="text-align:center">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    @foreach($days as $day)
                        <td style="text-align:center">
                            @php $status = $attendanceMap[$student->id][$day] ?? null; @endphp
                            @if($status === 'present')
                                P
                            @elseif($status === 'absent')
                                A
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    <td style="text-align:center">{{ $summary[$student->id]['present'] ?? 0 }}</td>
                    <td style="text-align:center">{{ $summary[$student->id]['total'] ?? 0 }}</td>
                    <td style="text-align:center">{{ $summary[$student->id]['percentage'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 