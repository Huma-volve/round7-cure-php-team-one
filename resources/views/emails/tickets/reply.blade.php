@php($contactName = $ticket->contact_name ?? $ticket->user?->name ?? 'عميلنا العزيز')

مرحباً {{ $contactName }},

لقد تم الرد على التذكرة رقم #{{ $ticket->id }} بخصوص "{{ $ticket->subject }}".

نص الرد:
{{ $message->message }}

يمكنك الرد مباشرة على هذا البريد أو من خلال التطبيق لمتابعة حالتك.

شكراً لاستخدامك {{ config('app.name') }}.

