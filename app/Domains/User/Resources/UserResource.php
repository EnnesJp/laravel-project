<?php

declare(strict_types=1);

namespace App\Domains\User\Resources;

use App\ValueObjects\Document\Factory\DocumentFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'document' => $this->formatDocument($this->document),
            'role'     => $this->role,
        ];
    }

    private function formatDocument(string $document): string
    {
        try {
            return DocumentFactory::create($document)->getFormatted();
        } catch (\InvalidArgumentException) {
            return $document;
        }
    }
}
