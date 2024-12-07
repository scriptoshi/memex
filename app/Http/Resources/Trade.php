<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Trade extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'launchpad_id' => $this->launchpad_id,
            'txid' => $this->txid,
            'address' => $this->address,
            'qty' => $this->qty,
            'amount' => $this->amount,
            'price' => bcdiv($this->amount, $this->qty, 18),
            'usd_price' => bcdiv($this->usd, $this->qty, 8),
            'usd' => $this->usd,
            'type' => $this->type,
            'date' => now()->gt($this->created_at->addDay())
                ? $this->created_at->toDateTimeString()
                : $this->created_at->diffForHumans(),
            'created_at' => $this->created_at,
            'launchpad' => new Launchpad($this->whenLoaded('launchpad')),
        ];
    }
}