<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetak QR Troli | In-Out Trolley</title>
        <style>
            :root {
                color-scheme: light;
                font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
                --page-padding: 0.6in;
                --card-padding: 0.85rem;
                --qr-size: 170px;
            }

            body {
                margin: 0;
                padding: 2rem 1.5rem;
                background: #0f172a;
                color: #e2e8f0;
            }

            header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 2rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            header h1 {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 600;
            }

            header p {
                margin: 0;
                font-size: 0.875rem;
                color: #94a3b8;
            }

            button {
                border: none;
                border-radius: 9999px;
                background: #22c55e;
                color: #0f172a;
                font-weight: 600;
                padding: 0.7rem 1.4rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
            }

            button:hover {
                background: #16a34a;
            }

            main {
                background: rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 1.5rem;
                padding: 1.5rem;
                page-break-inside: avoid;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
                gap: 0.9rem;
                align-items: start;
            }

            article {
                background: rgba(30, 41, 59, 0.9);
                border: 1px solid rgba(148, 163, 184, 0.15);
                border-radius: 1.25rem;
                padding: var(--card-padding);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0.75rem;
                height: 100%;
                box-sizing: border-box;
            }

            article h2 {
                margin: 0;
                font-size: 1.35rem;
                font-weight: 800;
                letter-spacing: 0.02em;
                color: #f1f5f9;
            }

            article p {
                margin: 0;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #94a3b8;
            }

            article img {
                width: var(--qr-size);
                height: var(--qr-size);
                object-fit: contain;
                background: white;
                padding: 0.75rem;
                border-radius: 0.75rem;
                box-shadow: 0 12px 25px rgba(15, 23, 42, 0.35);
                border: 1px solid rgba(148, 163, 184, 0.25);
            }

            footer {
                margin-top: 2rem;
                text-align: center;
                font-size: 0.75rem;
                color: #64748b;
            }

            @media print {
                body {
                    background: white;
                    color: black;
                    padding: var(--page-padding);
                }

                header, footer {
                    display: none;
                }

                main {
                    border: none;
                    padding: 0;
                background: transparent;
            }

            .grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.25in;
            }

            article {
                border: 1px solid #cbd5f5;
                background: white;
                box-shadow: none;
                color: black;
                padding: 0.5in 0.35in;
                page-break-inside: avoid;
            }

            article h2 {
                color: #0b1224;
                font-size: 1.3rem;
                font-weight: 800;
                letter-spacing: 0.03em;
            }

            article img {
                box-shadow: none;
                border: 1px solid #cbd5f5;
                width: 2.2in;
                height: 2.2in;
                padding: 0.18in;
            }
        }
        </style>
    </head>
    <body>
        <header>
            <div>
                <h1>Cetak QR Troli</h1>
                <p>
                    @if ($selectedCount)
                        Menampilkan {{ $selectedCount }} troli terpilih siap cetak.
                    @else
                        Menampilkan seluruh troli yang memiliki QR code aktif.
                    @endif
                </p>
            </div>
            <button type="button" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9V2h12v7" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <path d="M6 14h12v8H6z" />
                </svg>
                Cetak Sekarang
            </button>
        </header>

        <main>
            <div class="grid">
                @foreach ($trolleys as $trolley)
                    <article>
                        <h2>{{ $trolley->code }}</h2>
                        <img src="{{ asset('storage/' . $trolley->qr_code_path) }}" alt="QR Code Troli {{ $trolley->code }}">
                    </article>
                @endforeach
            </div>
        </main>

        <footer>
            In-Out Trolley — dioptimalkan untuk cetak 6–8 QR per halaman.
        </footer>
    </body>
</html>
