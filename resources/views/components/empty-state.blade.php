@props([
    'icon' => 'bi-inbox',
    'title' => 'Belum ada data',
    'message' => 'Saat ini belum ada data yang dapat ditampilkan.',
    'actionText' => null,
    'actionUrl' => null,
])

<div class="text-center py-5 px-3">
    <div class="mb-3">
        <i class="bi {{ $icon }} text-muted" style="font-size: 3.5rem; opacity: 0.6;"></i>
    </div>
    <h5 class="fw-bold mb-2">{{ $title }}</h5>
    <p class="text-muted mb-4 mx-auto" style="max-width: 420px;">{{ $message }}</p>
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-caramel">
            {{ $actionText }}
        </a>
    @endif
</div>
