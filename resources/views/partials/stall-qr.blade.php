{{-- Stall QR poster with download and print actions. Shared by the admin QR page and the staff QR tab.
     Needs: $stall, $url, $qr (data URI), $fileName. Anything marked .no-print is left off the printout. --}}
<div class="no-print flex flex-wrap items-center justify-end gap-2">
    <button type="button" id="download-png" class="btn btn-secondary btn-sm font-bold">
        <ion-icon name="download-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Download PNG
    </button>
    <a href="{{ $qr }}" download="{{ $fileName }}.svg" class="btn btn-secondary btn-sm font-bold">
        <ion-icon name="download-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Download SVG
    </a>
    <button type="button" onclick="window.print()" class="btn btn-primary btn-sm font-bold">
        <ion-icon name="print-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Print
    </button>
</div>

@if(! $stall->is_active)
    <p class="no-print mt-4 px-4 py-3 rounded-md border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900">
        {{ $stall->name }} is currently closed for evaluations. Students who scan this code will be told it is closed until an administrator reopens the stall.
    </p>
@endif

<div class="qr-poster mt-4 bg-white rounded-xl border border-neutral-200/80 px-6 py-10 sm:py-12 text-center">
    <p class="text-sm font-bold text-brand-700">ISU Cauayan Canteen Evaluation</p>
    <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900">{{ $stall->name }}</h2>
    <p class="mt-2 text-sm text-neutral-600">Scan with your phone camera to rate this stall.</p>

    <img id="qr-image" src="{{ $qr }}" alt="QR code linking to the evaluation form for {{ $stall->name }}"
         class="mx-auto mt-8 w-64 h-64 sm:w-80 sm:h-80" width="320" height="320">

    <p class="mt-6 text-xs text-neutral-500">Sign in with your student account to submit your evaluation.</p>
    <p class="mt-1 text-[11px] text-neutral-500 break-all">{{ $url }}</p>
</div>

<script>
    document.getElementById('download-png').addEventListener('click', function () {
        var size = 1024, pad = 64, labelHeight = 120;
        var canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size + labelHeight;
        var ctx = canvas.getContext('2d');
        var img = new Image();

        img.onload = function () {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.imageSmoothingEnabled = false;
            ctx.drawImage(img, pad, pad, size - pad * 2, size - pad * 2);
            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 48px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(@json($stall->name), size / 2, size + 20, size - pad * 2);

            var link = document.createElement('a');
            link.download = @json($fileName . '.png');
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        img.src = document.getElementById('qr-image').src;
    });
</script>
