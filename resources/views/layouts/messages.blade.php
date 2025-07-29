@if (session('display_msg'))
    @php
        $msg = session('display_msg');
    @endphp
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div id="message" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    @if (!empty($msg['icon']))
                        <i class="{{ $msg['icon'] }}"></i>
                    @endif
                    {!! $msg['msg'] !!}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var message = document.getElementById('message');
            if (message) {
                var toastPlacement = new bootstrap.Toast(message);
                toastPlacement.show();
            }
        });
    </script>
@endif
