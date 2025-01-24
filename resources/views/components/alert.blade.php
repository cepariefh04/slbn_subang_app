@props(['type' => 'success', 'message' => null])

@if ($message)
  <div class="alert border-0 bg-light-{{ $type }} alert-dismissible fade show py-2 mt-4" id="alert">
    <div class="d-flex align-items-center">
      <div class="fs-3 text-{{ $type }}">
        @if ($type === 'success')
          <i class="bi bi-check-circle-fill"></i>
        @elseif ($type === 'danger')
          <i class="bi bi-exclamation-circle-fill"></i>
        @elseif ($type === 'warning')
          <i class="bi bi-exclamation-triangle-fill"></i>
        @else
          <i class="bi bi-info-circle-fill"></i>
        @endif
      </div>
      <div class="ms-3">
        <div class="text-{{ $type }}">{{ $message }}</div>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
