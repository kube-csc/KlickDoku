<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .meta {
            color: #6b7280;
            font-size: 14px;
        }
        .meta span {
            margin-right: 20px;
        }
        .description {
            background: #f3f4f6;
            padding: 15px;
            border-left: 4px solid #2563eb;
            margin-bottom: 30px;
        }
        .step {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .step-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .step-number {
            font-weight: bold;
            font-size: 16px;
        }
        .step-content {
            padding: 10px 15px;
        }
        .tip {
            background: #d1fae5;
            border-left: 4px solid #059669;
            padding: 10px;
            margin-top: 10px;
        }
        .tip-title {
            font-weight: bold;
            color: #059669;
        }
        .warning {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 10px;
            margin-top: 10px;
        }
        .warning-title {
            font-weight: bold;
            color: #dc2626;
        }
        .image {
            margin: 15px 0;
            text-align: center;
        }
        .image img {
            max-width: 100%;
            height: auto;
            border: 1px solid #e5e7eb;
        }
        .image-caption {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .tags {
            margin-top: 20px;
        }
        .tag {
            display: inline-block;
            background: #e5e7eb;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @if($guide->category)
    <div class="header">
        <h1>{{ $guide->title }}</h1>
        <div class="meta">
            <span>📁 {{ $guide->category->title }}</span>
            <span>📅 {{ now()->format('d.m.Y') }}</span>
            <span>📝 {{ $guide->steps->count() }} Schritte</span>
        </div>
    </div>
    @else
    <div class="header">
        <h1>{{ $guide->title }}</h1>
        <div class="meta">
            <span>📅 {{ now()->format('d.m.Y') }}</span>
            <span>📝 {{ $guide->steps->count() }} Schritte</span>
        </div>
    </div>
    @endif

    @if($guide->description)
    <div class="description">
        {!! nl2br(e($guide->description)) !!}
    </div>
    @endif

    @if($guide->tags->isNotEmpty())
    <div class="tags">
        @foreach($guide->tags as $tag)
        <span class="tag">{{ $tag->name }}</span>
        @endforeach
    </div>
    @endif

    <div class="steps">
        @foreach($guide->steps as $step)
        <div class="step">
            <div class="step-header">
                <span class="step-number">Schritt {{ $step->order_number }}:</span>
                {{ $step->title }}
            </div>
            <div class="step-content">
                {!! nl2br(e($step->content)) !!}

                @if($step->tip)
                <div class="tip">
                    <div class="tip-title">💡 Tipp:</div>
                    {{ $step->tip }}
                </div>
                @endif

                @if($step->warning)
                <div class="warning">
                    <div class="warning-title">⚠️ Achtung:</div>
                    {{ $step->warning }}
                </div>
                @endif

                @if($step->images->isNotEmpty())
                @foreach($step->images as $image)
                <div class="image">
                    @if(file_exists(public_path('storage/' . $image->path)))
                    <img src="{{ public_path('storage/' . $image->path) }}" alt="{{ $image->alt_text ?? '' }}">
                    @endif
                    @if($image->caption)
                    <div class="image-caption">{{ $image->caption }}</div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Diese Anleitung wurde am {{ now()->format('d.m.Y H:i') }} Uhr als PDF exportiert.</p>
        <p>© {{ date('Y') }} Vereinsverwaltung</p>
    </div>
</body>
</html>
