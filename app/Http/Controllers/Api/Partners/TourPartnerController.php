<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Tour;
use App\Models\TourClient;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;
use Xendit\Invoice\CreateInvoiceRequest;
use Illuminate\Support\Facades\Http;

class TourPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = [];
        $nextWeek = Carbon::today()->addWeek();
        for ($i = Carbon::today(); $i < $nextWeek; $i = $i->addDay()) {
            $remaining = 10;
            $tour = Tour::where('tour_date', $i)->first();
            if ($tour)
                $remaining -= $tour->booked;
            $result[] = ['date' => $i, 'remaining' => $remaining];
        }
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns',
            'tour_date' => 'required|date',
        ]);
        $tour_date = new Carbon($data['tour_date']);
        $tour = Tour::where('tour_date', $tour_date)->first();
        if ($tour == null) {
            $tour = new Tour();
            $tour->tour_date = $tour_date;
            $tour->booked = 0;
            $tour->attended = 0;
            $tour->save();
        }
        $data['tour_id'] = $tour->id;
        $data['channel'] = 'PARTNERS';
        $data['is_booked'] = 0;
        $data['is_attended'] = 0;
        $saved_data = TourClient::create($data);
        // buat invoice ke xendit
        $invoice_description = "Order Tomo City Tour tanggal "
            . $tour_date->format('d M Y') . ' dengan Order Id.' . $saved_data->OrderId;
        Configuration::setXenditKey(config('services.xendit.secret_key'));
        $api = new InvoiceApi();
        $invoice_request = new CreateInvoiceRequest([
            'external_id' => $saved_data->unique_id,
            'amount' => 120000, // Convert to IDR
            'description' => $invoice_description,
            'currency' => 'IDR',
            'invoice_duration' => 3600, // 1 hour
        ]);
        $xendit_result = null;

        try {
            $xendit_result = $api->createInvoice($invoice_request);
        } catch (XenditSdkException $e) {
            return response()->json(
                [
                    "message" => $e->getMessage(),
                    "error" => $e->getFullError(),
                ]
            );
        }
        // kirim email via maileroo
        $mail_body = "<p>Selamat $saved_data->client_name !</p>" .
            '<p>Order Tomo City Tour tanggal ' . $tour_date->format('d M Y')
            . " dengan Order Id. $saved_data->OrderId telah berhasil.</p>" .
            '<p>Silakan melakukan pembayaran melalui ' . $xendit_result['invoice_url'] . '.';
        $payload = [
            "from" => [
                "address" => "jkjkjkama1@amalina.maileroo.org",
                "display_name" => "Amalina Tour"
            ],
            "to" => [
                [
                    "address" => $saved_data->email,
                    "display_name" => $saved_data->client_name,
                ]
            ],
            "subject" => "Invoice Amalina City Tour",
            "html" => $mail_body,
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Api-Key' => config('services.maileroo.api_key'),
        ])->post('https://smtp.maileroo.com/api/v2/emails', $payload);
        if (!$response->successful()) {
            return response()->json([
                'status' => 'error',
                'body' => $response->body(),
            ], $response->status());
        }
        return response()->json($saved_data, 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function book(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required',
            'external_id' => 'exists:tour_clients,unique_id',
            'user_id' => 'required',
        ]);
        $tour_client = TourClient::where('unique_id', $data['external_id'])->first();
        $tour = Tour::find($tour_client->tour_id);
        if ($tour->booked < 10) {
            $tour->booked++;
            $tour->save();
            $tour_client->is_booked = 1;
            $tour_client->save();
            $link = "http://localhost:8000/attend/" . $tour_client->unique_id;
            $mail_body = "<p>Selamat $tour_client->client_name !</p>" .
                '<p>Pembayaran Tomo City Tour tanggal '
                . (new Carbon($tour->tour_date))->format('d M Y')
                . " dengan Order Id. $tour_client->OrderId telah berhasil.</p>" .
                "<p>Silakan melakukan scan $link untuk menikmati paket wisata.</p>";
            $payload = [
                "from" => [
                    "address" => "jkjkjkama1@amalina.maileroo.org",
                    "display_name" => "Amalina City Tour"
                ],
                "to" => [
                    [
                        "address" => $tour_client->email,
                        "display_name" => $tour_client->client_name,
                    ]

                ],
                "subject" => "Payment Succeed Tomo City Tour",
                "html" => $mail_body,
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => config('services.maileroo.api_key'),
            ])->post('https://smtp.maileroo.com/api/v2/emails', $payload);
        } else {
            $mail_body = "<p>Hai $tour_client->client_name !</p>" .
                '<p>Pembayaran Tomo City Tour tanggal '
                . (new Carbon($tour->tour_date))->format('d M Y')
                . " dengan Order Id. $tour_client->OrderId dibatalkan"
                . " karena full-booked.</p>";
            $payload = [
                "from" => [
                    "address" => "tomo.city.tour@def59a2df3da65f4.maileroo.org",
                    "display_name" => "Tomo City Tour"
                ],
                "to" => [
                    [
                        "address" => $tour_client->email,
                        "display_name" => $tour_client->client_name,
                    ]
                ],
                "subject" => "Payment Cancelled Tomo City Tour",
                "html" => $mail_body,
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => config('services.maileroo.api_key'),
            ])->post('https://smtp.maileroo.com/api/v2/emails', $payload);
        }
        return response()->json(null);
    }
}
