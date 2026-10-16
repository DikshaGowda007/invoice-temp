Hi {{ $clientName }},

We've received your payment for invoice {{ $invoiceNumber }}, issued on {{ \Carbon\Carbon::parse($issueDate)->format('M j, Y') }}. Thank you!

Amount paid: ₹{{ number_format((float) $total, 2) }}

This is an automated receipt. No further action is needed.
