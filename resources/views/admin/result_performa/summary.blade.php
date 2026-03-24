@extends('layouts.admin_analytics-master')

@section('content')
<div class="container mt-4">
    <h4>Result Entry Permission Summary</h4>

    @foreach($classes as $class)
        <div class="card mb-4">
            <div class="card-header font-weight-bold">
                Class: {{ $class }}
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Term</th>
                            <th>Allowed Teachers</th>
                            <th>Not Allowed Teachers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($components->groupBy('term.name') as $termName => $termComponents)

                            <tr class="table-secondary">
                                <td colspan="3"><strong>{{ $termName }}</strong></td>
                            </tr>

                            @foreach($termComponents as $component)

                            @php
                                $allowedIds = $permissions[$class][$component->id] ?? [];
                            @endphp

                            <tr>
                                <td>{{ $component->name }}</td>

                                <td class="text-success">
                                    @forelse($teachers->whereIn('id', $allowedIds) as $t)
                                        <span class="badge badge-success">{{ $t->name }}</span>
                                    @empty
                                        <span class="text-muted">None</span>
                                    @endforelse
                                </td>

                                <td class="text-danger">
                                    @forelse($teachers->whereNotIn('id', $allowedIds) as $t)
                                        <span class="badge badge-light">{{ $t->name }}</span>
                                    @empty
                                        <span class="text-muted">All Allowed</span>
                                    @endforelse
                                </td>
                            </tr>

                            @endforeach
                            @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
