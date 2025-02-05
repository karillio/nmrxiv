<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'doi' => $this->doi,
            'title' => $this->title,
            'authors' => $this->authors,
            'abstract' => $this->abstract,
            'citation_text' => $this->citation_text,
        ];
    }
}
