<!DOCTYPE html>
<html lang="it">
    <body style="font-family: Arial, sans-serif; color: #1f2937;">
        <p>È stata pubblicata una nuova circolare sul sito della scuola:</p>

        <h2 style="margin: 16px 0 8px;">{{ $circular->title }}</h2>

        @if ($circular->published_at)
            <p style="margin: 0 0 16px; color: #6b7280;">
                Pubblicata il {{ $circular->published_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
            </p>
        @endif

        <p>
            <a href="{{ $circular->url }}">Leggi la circolare</a>
        </p>
    </body>
</html>
