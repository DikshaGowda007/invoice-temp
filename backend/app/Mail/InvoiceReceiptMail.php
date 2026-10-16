<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public Client $client,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment received for invoice '.$this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_receipt',
            text: 'emails.invoice_receipt_text',
            with: [
                'clientName' => $this->client->name,
                'invoiceNumber' => $this->invoice->invoice_number,
                'total' => $this->invoice->total,
                'issueDate' => $this->invoice->issue_date,
            ],
        );
    }
}
