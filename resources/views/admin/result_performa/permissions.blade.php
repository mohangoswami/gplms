@extends('layouts.admin_analytics-master')

@section('content')
<div class="container mt-4">
    <h4>Result Entry Permissions</h4>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- ==========================
         Selection Controls
    ========================== --}}
    <div class="card mb-3">
        <div class="card-body row">
            <div class="col-md-4">
                <label>Teacher</label>
                <select id="teacherSelect" class="form-control">
                    <option value="">-- Select Teacher --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Class</label>
                <select id="classSelect" class="form-control" disabled>
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ==========================
         Permission Form
    ========================== --}}
    <form method="POST" action="{{ route('admin.result.permissions.save') }}">
        @csrf

        <input type="hidden" name="teacher_id" id="formTeacher">
        <input type="hidden" name="class" id="formClass">

        <div id="permissionTable" style="display:none">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Component-wise Entry Permission
                </div>

                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Term</th>
                                <th>Component</th>
                                <th>Type</th>
                                <th>Max Marks</th>
                                <th>Allow Entry</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($components as $component)
                                <tr>
                                    <td>{{ $component->term->name }}</td>
                                    <td>{{ $component->name }}</td>
                                    <td>{{ ucfirst($component->evaluation_type) }}</td>
                                    <td>{{ $component->max_marks }}</td>
                                    <td>
                                        <input type="checkbox"
                                            name="components[]"
                                            value="{{ $component->id }}"
                                            class="component-checkbox">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button class="btn btn-primary">
                        Save Permissions
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('footerScript')
<script>
document.getElementById('teacherSelect').addEventListener('change', function () {
    document.getElementById('classSelect').disabled = !this.value;
    document.getElementById('formTeacher').value = this.value;
    resetTable();
});

document.getElementById('classSelect').addEventListener('change', function () {
    document.getElementById('formClass').value = this.value;
    document.getElementById('permissionTable').style.display =
        this.value ? 'block' : 'none';
});

function resetTable() {
    document.getElementById('permissionTable').style.display = 'none';
    document.querySelectorAll('.component-checkbox')
        .forEach(cb => cb.checked = false);
}

</script>

<script>
const teacherSelect = document.getElementById('teacherSelect');
const classSelect   = document.getElementById('classSelect');
const tableBox      = document.getElementById('permissionTable');

function resetCheckboxes() {
    document.querySelectorAll('.component-checkbox')
        .forEach(cb => cb.checked = false);
}

function loadPermissions() {

    const teacherId = teacherSelect.value;
    const className = classSelect.value;

    resetCheckboxes();

    if (!teacherId || !className) {
        tableBox.style.display = 'none';
        return;
    }

    tableBox.style.display = 'block';

    fetch(`{{ route('admin.result.permissions.fetch') }}?teacher_id=${teacherId}&class=${className}`)
    .then(res => {
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        console.log('Permissions response:', data);
        if (data.components) {
            data.components.forEach(id => {
                const cb = document.querySelector(
                    `.component-checkbox[value="${id}"]`
                );
                if (cb) cb.checked = true;
            });
        }
    })
    .catch(err => {
        console.error('Fetch failed:', err);
        alert('Failed to load permissions (check console)');
    });

}

teacherSelect.addEventListener('change', loadPermissions);
classSelect.addEventListener('change', loadPermissions);
</script>

@endsection


