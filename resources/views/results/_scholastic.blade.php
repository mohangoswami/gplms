<table border="1" width="100%" cellspacing="0" cellpadding="4">
<thead>
<tr>
    <th rowspan="2">Subject</th>
    <th colspan="5">Term – I</th>
    <th colspan="5">Term – II</th>
</tr>
<tr>
    <th>PT</th>
    <th>NB</th>
    <th>SE</th>
    <th>HY</th>
    <th>Total</th>

    <th>PT</th>
    <th>NB</th>
    <th>SE</th>
    <th>AN</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
@foreach($results['subjects'] as $subject => $data)

<tr>
    <td>{{ $subject }}</td>

    {{-- ===== TERM I ===== --}}
    <td>{{ $data['P1']['PT']['marks'] ?? '-' }}</td>
    <td>{{ $data['P1']['Notebook']['marks'] ?? '-' }}</td>
    <td>{{ $data['P1']['SE']['marks'] ?? '-' }}</td>

    {{-- HY shown in Term-I block --}}
    <td>
        @if($data['__mode'] === 'MARKS')
            {{ $data['HY']['Written']['marks'] ?? '-' }}
        @else
            {{ $data['P1']['grade'] ?? '-' }}
        @endif
    </td>

    <td>
        {{ $data['P1']['total'] ?? '-' }}
    </td>

    {{-- ===== TERM II ===== --}}
    <td>{{ $data['P2']['PT']['marks'] ?? '-' }}</td>
    <td>{{ $data['P2']['Notebook']['marks'] ?? '-' }}</td>
    <td>{{ $data['P2']['SE']['marks'] ?? '-' }}</td>

    <td>
        @if($data['__mode'] === 'MARKS')
            {{ $data['AN']['Written']['marks'] ?? '-' }}
        @else
            {{ $data['P2']['grade'] ?? '-' }}
        @endif
    </td>

    <td>
        {{ $data['P2']['total'] ?? '-' }}
    </td>
</tr>

@endforeach
</tbody>
</table>
