<form method="POST"
      action="{{ route('result-performa.seed') }}"
      onsubmit="return confirm('Create default Result Performas for all classes?')">
    @csrf
    <button class="btn btn-warning btn-sm">
        Run Result Performa Seeder
    </button>
</form>
