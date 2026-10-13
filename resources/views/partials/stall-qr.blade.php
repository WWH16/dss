{{-- Stall QR poster with download and print actions. Shared by the admin QR page and the staff QR tab.
     Needs: $stall, $url, $qr (data URI), $fileName. Anything marked .no-print is left off the printout. --}}
{{-- CHANGED: on phones the actions sit in a two-column grid with 44px targets, and the SVG download is desktop-only. --}}
{{-- CHANGED: Print is the primary action and comes first, the row is left-aligned with the page heading, and the Share button is gone so the code is used at the counter rather than passed around group chats. --}}
<div class="no-print grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
    <button type="button" onclick="window.print()" class="btn btn-primary btn-sm font-bold min-h-11 sm:min-h-0">
        <ion-icon name="print-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Print
    </button>
    <button type="button" id="download-png" class="btn btn-secondary btn-sm font-bold min-h-11 sm:min-h-0">
        <ion-icon name="download-outline" class="text-sm" aria-hidden="true"></ion-icon>
        Download PNG
    </button>
    {{-- CHANGED: the Download SVG link is gone; Print and Download PNG cover the poster. --}}
</div>

@if(! $stall->is_active)
    <p class="no-print mt-4 px-4 py-3 rounded-md border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900">
        {{ $stall->name }} is currently closed for evaluations. Students who scan this code will be told it is closed until an administrator reopens the stall.
    </p>
@endif

{{-- CHANGED: print: classes turn the card into a full-page poster (no frame, bigger type, a 12 cm code readable from across the counter). --}}
<div class="qr-poster mt-4 bg-white rounded-xl border border-neutral-200/80 px-4 py-8 sm:px-6 sm:py-12 text-center print:mt-0 print:p-0 print:border-0 print:rounded-none">
    {{-- CHANGED: the ISU logo sits beside the title so students can tell the code is the university's. --}}
    <p id="qr-brand" class="flex items-center justify-center gap-2 text-sm font-bold text-brand-700 print:gap-3 print:text-xl">
        <img id="qr-logo" src="{{ asset('assets/images/isu_logo.png') }}" alt="" width="32" height="32" class="size-8 print:size-14">
        ISU Cauayan Canteen Evaluation
    </p>
    <h2 id="qr-name" class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 text-balance print:mt-6 print:text-5xl">{{ $stall->name }}</h2>
    <p id="qr-hint" class="mt-2 text-sm text-neutral-600 print:mt-3 print:text-xl">Scan with your phone camera to rate this stall.</p>

    <img id="qr-image" src="{{ $qr }}" alt="QR code linking to the evaluation form for {{ $stall->name }}"
         class="mx-auto mt-6 sm:mt-8 w-full max-w-[16rem] sm:max-w-[20rem] h-auto aspect-square print:mt-10 print:max-w-[12cm]" width="320" height="320">
    {{-- CHANGED: the sign-in note and the raw signed URL are no longer printed below the code. --}}
</div>

<script>
(function () {
    var pngName = @json($fileName . '.png');

    // CHANGED: the PNG is drawn when Download PNG is tapped and copies the card: logo and title, stall name
    // (wrapped when long), scan instruction, then the code, in the card's own font and colours.
    function wrap(ctx, text, maxWidth) {
        var lines = [], line = '';
        text.split(' ').forEach(function (word) {
            var next = line ? line + ' ' + word : word;
            if (line && ctx.measureText(next).width > maxWidth) {
                lines.push(line);
                line = word;
            } else {
                line = next;
            }
        });
        lines.push(line);
        return lines;
    }

    document.getElementById('download-png').addEventListener('click', function () {
        var brand = document.getElementById('qr-brand');
        var name = document.getElementById('qr-name');
        var hint = document.getElementById('qr-hint');
        var family = getComputedStyle(name).fontFamily;
        var width = 1200, pad = 96, logo = 72, nameSize = 88, nameLine = 104, qr = 840;

        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        ctx.font = '700 ' + nameSize + 'px ' + family;
        var nameLines = wrap(ctx, name.textContent.trim(), width - pad * 2);

        canvas.width = width;
        canvas.height = pad + logo + 40 + nameLines.length * nameLine + 16 + 48 + 64 + qr + pad;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        var y = pad;
        var title = brand.textContent.trim();
        ctx.font = '700 36px ' + family;
        var rowX = (width - (logo + 20 + ctx.measureText(title).width)) / 2;
        ctx.drawImage(document.getElementById('qr-logo'), rowX, y, logo, logo);
        ctx.fillStyle = getComputedStyle(brand).color;
        ctx.textBaseline = 'middle';
        ctx.fillText(title, rowX + logo + 20, y + logo / 2);
        y += logo + 40;

        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.font = '700 ' + nameSize + 'px ' + family;
        ctx.fillStyle = getComputedStyle(name).color;
        nameLines.forEach(function (line) {
            ctx.fillText(line, width / 2, y);
            y += nameLine;
        });
        y += 16;

        ctx.font = '400 40px ' + family;
        ctx.fillStyle = getComputedStyle(hint).color;
        ctx.fillText(hint.textContent.trim(), width / 2, y, width - pad * 2);
        y += 48 + 64;

        ctx.imageSmoothingEnabled = false;
        ctx.drawImage(document.getElementById('qr-image'), (width - qr) / 2, y, qr, qr);

        var link = document.createElement('a');
        link.download = pngName;
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
})();
</script>
