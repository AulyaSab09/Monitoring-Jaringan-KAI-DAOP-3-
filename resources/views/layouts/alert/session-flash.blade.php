{{-- Display Session Flash Messages --}}
@if (session('success'))
    @include('layouts.alert.notification', ['type' => 'success', 'message' => session('success')])
@endif

@if (session('error'))
    @include('layouts.alert.notification', ['type' => 'error', 'message' => session('error')])
@endif

@if (session('warning'))
    @include('layouts.alert.notification', ['type' => 'warning', 'message' => session('warning')])
@endif

@if (session('info'))
    @include('layouts.alert.notification', ['type' => 'info', 'message' => session('info')])
@endif
