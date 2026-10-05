{{-- Stall QR poster with share, download and print actions. Shared by the admin QR page and the staff QR tab.
     Needs: $stall, $url, $qr (data URI), $fileName. Anything marked .no-print is left off the printout. --}}
{{-- CHANGED: on phones the actions sit in a two-column grid with 44px targets, Share (where the browser can share files) spans the top row, and the SVG download is desktop-only. --}}
<div class="no-print grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
    <button type="button" id="share-qr" class="btn btn-primary btn-sm font-bold col-span-2 min-h-11 sm:min-h-0 hidden!">
        <ion-icon name="share-social-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Share
    </button>
    <button type="button" id="download-png" class="btn btn-secondary btn-sm font-bold min-h-11 sm:min-h-0">
        <ion-icon name="download-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Download PNG
    </button>
    <a href="{{ $qr }}" download="{{ $fileName }}.svg" class="btn btn-secondary btn-sm font-bold hidden! sm:inline-flex!">
        <ion-icon name="download-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Download SVG
    </a>
    <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm font-bold min-h-11 sm:min-h-0 sm:order-last">
        <ion-icon name="print-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Print
    </button>
</div>

@if(! $stall->is_active)
    <p class="no-print mt-4 px-4 py-3 rounded-md border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900">
        {{ $stall->name }} is currently closed for evaluations. Students who scan this code will be told it is closed until an administrator reopens the stall.
    </p>
@endif

<div class="qr-poster mt-4 bg-white rounded-xl border border-neutral-200/80 px-4 py-8 sm:px-6 sm:py-12 text-center">
    <p class="text-sm font-bold text-brand-700">ISU Cauayan Canteen Evaluation</p>
    <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 text-balance">{{ $stall->name }}</h2>
    <p class="mt-2 text-sm text-neutral-600">Scan with your phone camera to rate this stall.</p>

    <img id="qr-image" src="{{ $qr }}" alt="QR code linking to the evaluation form for {{ $stall->name }}"
         class="mx-auto mt-6 sm:mt-8 w-full max-w-[16rem] sm:max-w-[20rem] h-auto aspect-square" width="320" height="320">

    <p class="mt-6 text-xs text-neutral-500">Sign in with your student account to submit your evaluation.</p>
    <p class="mt-1 text-[11px] text-neutral-500 break-all">{{ $url }}</p>
</div>

<script>
(function () {
    var stallName = @json($stall->name);
    var pngName = @json($fileName . '.png');
    var pngFile = null;

    // CHANGED: the PNG is drawn once on load and kept, so Download and Share both use it, and Share can
    // open the share sheet straight from the tap (browsers refuse share() after an async wait).
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
        ctx.fillText(stallName, size / 2, size + 20, size - pad * 2);
        canvas.toBlob(function (blob) {
            pngFile = new File([blob], pngName, { type: 'image/png' });
            // CHANGED: Share (phone share sheet: Messenger, Gallery, ...) only appears where files can be shared.
            if (navigator.canShare && navigator.canShare({ files: [pngFile] })) {
                document.getElementById('share-qr').classList.remove('hidden!');
            }
        }, 'image/png');
    };
    img.src = document.getElementById('qr-image').src;

    document.getElementById('download-png').addEventListener('click', function () {
        var link = document.createElement('a');
        link.download = pngName;
        link.href = pngFile ? URL.createObjectURL(pngFile) : canvas.toDataURL('image/png');
        link.click();
        if (pngFile) setTimeout(function () { URL.revokeObjectURL(link.href); }, 1000);
    });

    document.getElementById('share-qr').addEventListener('click', function () {
        if (!pngFile) return;
        navigator.share({ files: [pngFile], title: stallName + ' QR code', text: 'Scan to rate ' + stallName + '.' })
            .catch(function () {});
    });
})();
</script>
