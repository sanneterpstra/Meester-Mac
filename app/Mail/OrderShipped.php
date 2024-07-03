<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use LemmoTresto\Moneybird\MoneybirdFacade as Moneybird;
use Lunar\Models\Order;
use Picqer\Financials\Moneybird\Entities\SalesInvoice\SendInvoiceOptions;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order
    ) {
        $this->pdf = base64_encode($this->generateMoneybirdInvoice()->download());
    }

    protected function generateMoneybirdInvoice()
    {

        // Get contact details
        $moneybirdContact = Moneybird::contact()->findByCustomerId($this->order->customer->meta['moneybird_customer_number']);

        // Create invoice object
        $moneybirdInvoice = Moneybird::salesInvoice();
        $moneybirdInvoice->contact_id = $moneybirdContact->id;
        $moneybirdInvoice->prices_are_incl_tax = true;

        // Physical lines
        foreach ($this->order->physicalLines as $line) {
            $salesInvoiceDetail = Moneybird::salesInvoiceDetail();
            $salesInvoiceDetail->amount = $line->quantity.'x';
            $salesInvoiceDetail->description = $line->description.($line->option ? '<br> _'.$line->option.'_' : '');
            $salesInvoiceDetail->price = $line->unit_price->decimal;
            $salesInvoiceDetailsArray[] = $salesInvoiceDetail;
        }
        // Shipping lines
        foreach ($this->order->shippingLines as $line) {
            $salesInvoiceDetail = Moneybird::salesInvoiceDetail();
            $salesInvoiceDetail->amount = $line->quantity.'x';
            $salesInvoiceDetail->description = $line->description;
            $salesInvoiceDetail->price = $line->sub_total->decimal;
            $salesInvoiceDetailsArray[] = $salesInvoiceDetail;
        }

        $moneybirdInvoice->details = $salesInvoiceDetailsArray;
        $salesInvoice = $moneybirdInvoice->save();
        $salesInvoice->sendInvoice(SendInvoiceOptions::METHOD_MANUAL);

        foreach ($this->order->transactions as $transaction) {
            if ($transaction->success && $transaction->status == 'paid') {
                $salesInvoicePayment = Moneybird::salesInvoicePayment();
                $salesInvoicePayment->price = $transaction->amount->decimal;
                $salesInvoicePayment->payment_date = $transaction->updated_at->format('Y-m-d');
                $salesInvoice->registerPayment($salesInvoicePayment);
            }
        }

        return $salesInvoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bestelling verstuurd',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.shipped',
            with: [
                'title' => 'Je pakket is onderweg',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => base64_decode($this->pdf), 'factuur.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
